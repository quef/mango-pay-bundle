<?php

/**
 * MangoPayBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\MangoPayBundle\Entity\Interfaces\User;

/**
 * Interface FullNaturalUserInterface.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface FullNaturalUserInterface extends NaturalUserInterface
{
    /**
     * @return AddressInterface
     */
    public function getAddressObject();

    /**
     * @return string
     */
    public function getOccupation();

    /**
     * @return string
     */
    public function getIncomeRange();

    /**
     * @return string
     */
    public function getProofOfIdentity();

    /**
     * @return string
     */
    public function getProofOfAddress();
}
