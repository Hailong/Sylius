<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AlipayWapGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gateway_url', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.gateway_url',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.gateway_url.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('app_id', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.app_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.app_id.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('rsa_private_key', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.rsa_private_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.rsa_private_key.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('rsa_public_key', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.rsa_public_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.rsa_public_key.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('charset', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.charset',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.charset.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->add('sign_type', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.alipay.sign_type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.alipay.sign_type.not_blank',
                        'groups' => 'sylius',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                $data['payum.http_client'] = '@sylius.payum.http_client';
            })
        ;
    }
}
