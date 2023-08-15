<?php
namespace App\Service;

use App\Entity\EmailTracking;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailTrackingService
{
    private $doctrine;
    private $httpClient;

    public function __construct(ManagerRegistry $doctrine, HttpClientInterface $httpClient)
    {
        $this->doctrine = $doctrine;
        $this->httpClient = $httpClient;
    }

    public function trackEmailView(string $imageUrl, string $trackingId): void
    {
        try {
            $response = $this->httpClient->request('GET', $imageUrl);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $entityManager = $this->doctrine->getManager();

                $emailTrackingRepository = $entityManager->getRepository(EmailTracking::class);
                $emailTracking = $emailTrackingRepository->findOneBy(['trackingId' => $trackingId]);

                if (!$emailTracking) {
                    $emailTracking = new EmailTracking();
                    $emailTracking->setTrackedId($trackingId);
                    $entityManager->persist($emailTracking);
                }

                $emailTracking->setViewed(true);
                $emailTracking->incrementViewCount();

                $entityManager->flush();
            }
        } catch (ClientExceptionInterface $e) {
        }
    }
}
