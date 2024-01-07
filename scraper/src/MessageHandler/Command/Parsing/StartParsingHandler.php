<?php

namespace App\MessageHandler\Command\Parsing;


use App\Message\Parsing\StartParsingCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StartParsingHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    public function __construct(private readonly LoggerInterface $int) {}
    public function __invoke(StartParsingCommand $parsingCommand) {

    }

}