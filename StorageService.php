<?php

namespace Bizapps\Rdp\Services;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Exception;
use Throwable;

class StorageService
{
    private function sendRequest(string $url, string $method, array $queryData = [], $json = true, $multi = false)
    {
        $httpClient = new HttpClient();
        $httpClient->setHeader('Accept', 'application/json');
        if (in_array($method, ['put', 'post', 'patch']) && $json) {
            $httpClient->setHeader('Content-type', 'application/json');
            //$queryData = json_encode($queryData, JSON_UNESCAPED_UNICODE);
        }
        switch ($method) {
            case 'put':
            {
                $httpClient->query(HttpClient::HTTP_PUT, $url, $queryData);
                $response = $httpClient->getResult();
                break;
            }
            case 'post':
            {
                $response = $httpClient->post(
                    $url,
                    $queryData,
                    $multi
                );
                break;
            }
            case 'get':
            default:
            {
                $response = $httpClient->get(
                    $url . '?' . http_build_query($queryData));
                break;
            }
        }
        if ($httpClient->getStatus() !== 200 && $httpClient->getStatus() !== 201) {
            if($httpClient->getError())
            {
                throw new Exception('Ошибка: ' . Json::encode($httpClient->getError()));
            }
            throw new Exception('Ошибка: ' . ($response ? Json::decode($response)['detail'] : ''));
        }

        return $response;
    }

    public function blobUploadPrivate(array $fields)
    {
        try {
            $data = $this->sendRequest(
                $_ENV['STORAGE_API_SERVICE'] . 'api/Blob/upload-private',
                'post',
                $fields,
                false,
                true
            );
        } catch (Throwable $e) {
            return ['error' => $e->getMessage()];
        }
        return Json::decode($data);
    }
}