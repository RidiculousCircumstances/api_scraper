<?php

namespace App\Controller;

use App\Entity\DataSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: "test")]
    public function test(EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $dataSchema = new DataSchema();
        $dataSchema
            ->setName("test name")
            ->setUrl("www.example.com");


        $errors = $validator->validate($dataSchema);

        if(count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($dataSchema);
        $entityManager->flush();

        return new Response('Saved new product with id '.$dataSchema->getId());
    }
}