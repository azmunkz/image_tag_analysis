<?php

namespace Drupal\image_tag_analysis\Service;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

class OpenAiAssistantService {

  protected $httpClient;
  protected $logger;
  protected $apiKey;
  protected $assistantId;

  public function __construct(ClientInterface $http_client, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $http_client;
    $this->logger = $logger_factory->get('image_tag_analysis');

    // Set your API Key and Assistant ID here (or from config)
    $key_service = \Drupal::service('key.repository');
    $api_key = $key_service->getKey('openai_key')->getKeyValue();
    $this->apiKey = $api_key;

    // Set Assistant ID
    $config = \Drupal::config('image_tag_analysis.settings');
    $this->assistantId = $config->get('assistant_id'); // from Kanda's assistant
  }

  /**
   * Allows changing assistant ID dynamically.
   */
  public function setAssistantId(string $assistantId): void {
    $this->assistantId = $assistantId;
  }

  /**
   * Step 1: Create a new thread.
   */
  public function createThread(): ?string {
    try {
      $response = $this->httpClient->post('https://api.openai.com/v1/threads', [
        'headers' => $this->getHeaders(),
      ]);

      $data = json_decode($response->getBody(), TRUE);
      return $data['id'] ?? NULL;
    } catch (\Exception $e) {
      $this->logger->error('Error creating thread: @msg', ['@msg' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Step 2: Add message with image URL.
   */
  public function addMessage(string $thread_id, string $image_url): bool {
    try {
      $response = $this->httpClient->post("https://api.openai.com/v1/threads/{$thread_id}/messages", [
        'headers' => $this->getHeaders(),
        'json' => [
          'role' => 'user',
          'content' => [
            [
              'type' => 'image_url',
              'image_url' => [
                'url' => $image_url,
              ],
            ],
            [
              'type' => 'text',
              'text' => $this->getPromptText($thread_id),
            ]
          ],
        ],
      ]);

      return $response->getStatusCode() === 200;
    } catch (\Exception $e) {
      $this->logger->error('Error adding message: @msg', ['@msg' => $e->getMessage()]);
      return FALSE;
    }
  }

  private function getHeaders(): array {
    return [
      'Authorization' => 'Bearer ' . $this->apiKey,
      'OpenAI-Beta' => 'assistants=v2',
      'Content-Type' => 'application/json',
    ];
  }

  private function getPromptText($thread_id): string {
    return 'Analyze the uploaded image and return structured product metadata including product name, brand, category, visual features, and a clean array of short, matchable tags. Use the same format as described in your system instructions. Ensure that the description field reflects what is visually seen in the image. Do not use generic or placeholder descriptions.';
  }


  /**
   * Step 3: Run the Assistant.
   */
  public function runAssistant(string $thread_id): ?string {
    try {
      $response = $this->httpClient->post("https://api.openai.com/v1/threads/{$thread_id}/runs", [
        'headers' => $this->getHeaders(),
        'json' => [
          'assistant_id' => $this->assistantId,
        ],
      ]);

      $data = json_decode($response->getBody(), TRUE);
      return $data['id'] ?? NULL;
    } catch (\Exception $e) {
      $this->logger->error('Error running assistant: @msg', ['@msg' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Step 4: Poll the run status until complete.
   */
  public function pollUntilComplete(string $thread_id, string $run_id, int $timeout = 60): ?array {
    $elapsed = 0;

    try {
      while ($elapsed < $timeout) {
        sleep(2); // wait 2s before checking
        $elapsed += 2;

        $statusResponse = $this->httpClient->get("https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}", [
          'headers' => $this->getHeaders(),
        ]);

        $statusData = json_decode($statusResponse->getBody(), TRUE);
        $status = $statusData['status'] ?? 'unknown';

        if ($status === 'completed') {
          // Get the message content
          $messageResponse = $this->httpClient->get("https://api.openai.com/v1/threads/{$thread_id}/messages", [
            'headers' => $this->getHeaders(),
          ]);

          $messages = json_decode($messageResponse->getBody(), TRUE);
          foreach ($messages['data'] as $message) {
            if ($message['role'] === 'assistant') {
              foreach ($message['content'] as $content) {
                if ($content['type'] === 'text') {
                  return json_decode($content['text']['value'], TRUE);
                }
              }
            }
          }
          break;
        }

        if (in_array($status, ['failed', 'cancelled', 'expired'])) {
          $this->logger->error("Assistant run failed with status: @status", ['@status' => $status]);
          break;
        }
      }
    } catch (\Exception $e) {
      $this->logger->error('Error polling assistant: @msg', ['@msg' => $e->getMessage()]);
    }

    return NULL;
  }

}
