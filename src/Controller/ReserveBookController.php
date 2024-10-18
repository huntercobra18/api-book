<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Emprunt;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class ReserveBookController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route(
        name: 'reserve_book',
        path: '/api/books/{id}/reserve',
        methods: ['POST'],
    )]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new BadRequestHttpException('User not authenticated.');
        }

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $book = $bookRepository->findOneBy(['id' => $id]);

        if (!$book) {
            throw new NotFoundHttpException('Book not found.');
        }

        $empruntRepository = $this->entityManager->getRepository(Emprunt::class);
        $oneWeekAgo = (new \DateTimeImmutable())->modify('-7 days');
        $recentEmprunts = $empruntRepository->createQueryBuilder('e')
            ->where('e.emprunteur = :user')
            ->andWhere('e.date >= :oneWeekAgo')
            ->setParameter('user', $user)
            ->setParameter('oneWeekAgo', $oneWeekAgo)
            ->getQuery()
            ->getResult();

        if (count($recentEmprunts) >= 2) {
            throw new BadRequestHttpException('You cannot reserve more than 2 books in the same week.');
        }

        $reservation = new Emprunt();
        $reservation->setEmprunteur($user);
        $reservation->setLivre($book);
        $reservation->setDate(new \DateTimeImmutable());

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Book reserved successfully!'], JsonResponse::HTTP_CREATED);
    }
}
