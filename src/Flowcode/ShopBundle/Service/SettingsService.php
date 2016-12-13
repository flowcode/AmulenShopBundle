<?php

namespace Flowcode\ShopBundle\Service;

use Amulen\ShopBundle\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Flowcode\ShopBundle\Entity\ShopSetting;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Settings Service
 */
class SettingsService
{
    /**
     * @var EntityRepository
     */
    protected $shopSettingsRepository;

    public function __construct(EntityRepository $shopSettingsRepository)
    {
        $this->shopSettingsRepository = $shopSettingsRepository;
    }

    /**
     * Get the shop status.
     *
     * @return bool
     */
    public function shopIsAvailable()
    {
        $open = false;

        /* @var ShopSetting $setting */
        $setting = $this->shopSettingsRepository->findOneBy([
            'name' => \Amulen\ShopBundle\Entity\ShopSetting::SHOP_AVAILABLE
        ]);

        if ($setting) {
            $strValue = $setting->getValue();
            $value = strtolower(trim($strValue));
            if ($value == 'yes' || $value == 'si' || $value == 'on' || $value == 'true') {
                $open = true;
            }
        }

        return $open;
    }

    /**
     * Get s setting value.
     *
     * @param $settingName
     * @return null|string
     */
    public function getSetting($settingName)
    {
        $value = null;

        /* @var ShopSetting $setting */
        $setting = $this->shopSettingsRepository->findOneBy([
            'name' => $settingName
        ]);

        if ($setting) {
            $value = $setting->getValue();
        }

        return $value;
    }

    /**
     * @return EntityRepository
     */
    public function getShopSettingsRepository()
    {
        return $this->shopSettingsRepository;
    }

    /**
     * @param EntityRepository $shopSettingsRepository
     */
    public function setShopSettingsRepository($shopSettingsRepository)
    {
        $this->shopSettingsRepository = $shopSettingsRepository;
    }


}
