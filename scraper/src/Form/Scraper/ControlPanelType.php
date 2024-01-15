<?php

namespace App\Form\Scraper;

use App\Entity\DataSchema;
use App\Message\Scraper\Enum\OutputFormatsEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ControlPanelType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mOptionsData = m($options)(maybe_key('data'));

        /**
         * @var array<DataSchema> $dataSchemas
         */
        $dataSchemas = $mOptionsData
        (maybe_key('schemaEntities'))();

        $dataSchemaChoices = [];

        foreach ($dataSchemas as $dataSchema) {
            $dataSchemaChoices[$dataSchema->getName()] = $dataSchema->getId();
        }

        $formats = $mOptionsData
        (maybe_key('availableFormats'))();

        $formatChoices = [];

        /**
         * @var OutputFormatsEnum $format
         */
        foreach ($formats as $format) {
            $formatChoices[$format->value] = $format->value;
        }

        $methods = $mOptionsData
        (maybe_key('availableMethods'))();

        $methodChoices = [];

        foreach ($methods as $method) {
            $methodChoices[$method] = $method;
        }

        $builder
            ->add('schema', ChoiceType::class, [
                'choices' => $dataSchemaChoices,
                'label' => 'Использовать схему парсинга: ',
                'placeholder' => 'Выбрать...'
            ])
            ->add('format', ChoiceType::class, [
                'choices' => $formatChoices,
                'label' => 'Парсить в: ',
                'placeholder' => 'Выбрать...'
            ])
            ->add('file', TextType::class, [
                'label' => 'Имя файла: ',
                'attr' => ['placeholder' => '/drom_api_scraper/output']
            ])
            ->add('method', ChoiceType::class, [
                'required' => true,
                'choices' => $methodChoices,
                'label' => 'HTTP Метод: ',
                'placeholder' => 'Выбрать...'
            ])
            ->add('secret', TextType::class, [
                'required' => false,
                'label' => 'Секрет для подписи запроса(если требуется): ',
                'attr' => ['placeholder' => 'Укажите секрет...']
            ])
            ->add('auth', TextType::class, [
                'required' => false,
                'label' => 'Токен авторизации(если тербуется): ',
                'attr' => ['placeholder' => 'X-auth-token...']
            ])
            ->add('delay', IntegerType::class, [
                'required' => false,
                'label' => 'Задержка между запросами, ms: '
            ])
            ->add('useProxy', CheckboxType::class, [
                'required' => false,
                'label' => 'Использовать прокси: '
            ])
            ->add('run', SubmitType::class, [
                'label' => 'Начать парсинг'
            ])
            ->setAction($this->urlGenerator->generate('admin_start_parsing', [], UrlGeneratorInterface::RELATIVE_PATH));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setdefaults([]);
    }
}