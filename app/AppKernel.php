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

use Sylius\Bundle\CoreBundle\Application\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(), // Required by SyliusAdminApiBundle.
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new \Sylius\ShopApiPlugin\ShopApiPlugin(),
            new \League\Tactician\Bundle\TacticianBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),

            // new Lilocon\WechatBundle\LiloconWechatBundle(),

            new Zshwag\Bundle\WechatBundle\ZshwagWechatBundle(),
            new Orinoco\WechatBundle\OrinocoWechatBundle(),
        ];

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }
}
