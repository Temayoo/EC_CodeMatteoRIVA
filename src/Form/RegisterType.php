<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegisterType extends AbstractType
{
    //Fonction qui initialise les inputs : email/password/confirmPassword/agreesTerms
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new NotBlank([ //permet de ne pas laisser un input vide
                        'message' => 'L\'email ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe ne peut pas être vide.',
                    ]),
                    new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', // regex + message d'erreur
                        "Il faut un mot de passe avec plus de 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial"),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La confirmation du mot de passe ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes.',
                    ]),
                ],
            ])
        ;
        // attend un POST et verifie si password et confirmPassword sont pareil
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $password = $form->get('password')->getData();
                $confirmPassword = $form->get('confirmPassword')->getData();

                if ($password !== $confirmPassword) {
                    $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                }
            }
        );
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
