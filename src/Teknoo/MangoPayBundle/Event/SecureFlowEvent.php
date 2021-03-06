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
namespace Teknoo\MangoPayBundle\Event;

use MangoPay\PayIn;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Teknoo\MangoPayBundle\Entity\SecureFlowSession;

/**
 * Class SecureFlowEvent.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class SecureFlowEvent extends Event
{
    /**
     * @var SecureFlowSession
     */
    protected $secureFlowSession;

    /**
     * @var PayIn
     */
    protected $payIn;

    /**
     * @var Response
     */
    protected $response;

    /**n
     * @param PayIn $payIn
     * @param Response $response
     * @param SecureFlowSession|null $secureFlowSession
     */
    public function __construct(
        PayIn $payIn,
        Response $response,
        SecureFlowSession $secureFlowSession = null
    ) {
        $this->secureFlowSession = $secureFlowSession;
        $this->payIn = $payIn;
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return PayIn
     */
    public function getPayIn()
    {
        return $this->payIn;
    }

    /**
     * @return SecureFlowSession
     */
    public function getSecureFlowSession()
    {
        return $this->secureFlowSession;
    }
}
