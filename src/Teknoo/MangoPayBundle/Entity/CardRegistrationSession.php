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
namespace Teknoo\MangoPayBundle\Entity;

use Teknoo\MangoPayBundle\Entity\Interfaces\User\UserInterface;

/**
 * Class CardRegistrationSession.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CardRegistrationSession
{
    /**
     * @var string
     */
    protected $cardRegistrationId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var mixed
     */
    protected $businessData;

    /**
     * To initialize the session.
     */
    public function __construct()
    {
        $this->sessionId = \uniqid();
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getCardRegistrationId()
    {
        return $this->cardRegistrationId;
    }

    /**
     * @param string $cardRegistrationId
     *
     * @return $this
     */
    public function setCardRegistrationId($cardRegistrationId)
    {
        $this->cardRegistrationId = $cardRegistrationId;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     *
     * @return self
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * To return from session some values needed by your business services to process answer of Mango.
     *
     * @return mixed
     */
    public function getBusinessData()
    {
        return $this->businessData;
    }

    /**
     * To register in a session some values needed by your business services to process answer of Mango.
     *
     * @param mixed $businessData
     *
     * @return self
     */
    public function setBusinessData($businessData)
    {
        $this->businessData = $businessData;

        return $this;
    }
}
