<?php

namespace App\Controller\Parsing;

use App\Domain\Parsing\DTO\StartParsingCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class ParsingController extends AbstractController
{

    #[Route('/parse', 'admin_start_parsing')]
    public function parse(#[MapRequestPayload] StartParsingCommand $data): RedirectResponse
    {

        return $this->redirectToRoute('home');
    }

}