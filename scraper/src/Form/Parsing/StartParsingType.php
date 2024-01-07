<?php

namespace App\Form\Parsing;

use App\Entity\DataSchema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StartParsingType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mOptionsData = m($options)(maybe_key('data'));

        /**
         * @var array<DataSchema> $dataSchemas
         */
        $dataSchemas = $mOptionsData
            (maybe_key('schemaEntities'))();

        $dataSchemaChoices = [];

        foreach($dataSchemas as $dataSchema) {
            $dataSchemaChoices[$dataSchema->getName()] = $dataSchema->getId();
        }

        $formats = $mOptionsData
            (maybe_key('availableFormats'))();

        $formatChoices = [];

        foreach($formats as $format) {
            $formatChoices[$format] = $format;
        }

        $builder
            ->add('schema', ChoiceType::class, [
                'choices' => $dataSchemaChoices,
                'label' => 'Использовать схему данных: ',
                'placeholder' => 'Выбрать...'
            ])
            ->add('format', ChoiceType::class, [
                'choices' => $formatChoices,
                'label' => 'Парсить в: ',
                'placeholder' => 'Выбрать...'
            ])
            ->add('path', TextType::class, [
                'label' => 'Сохранять в: ',
                'attr' => ['placeholder' => 'Укажите путь от корня...']
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