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
namespace Teknoo\MangoPayBundle\Service;

use MangoPay\ApiCardRegistrations;
use MangoPay\CardRegistration;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Teknoo\MangoPayBundle\Exception\BadMangoEntityException;
use Teknoo\MangoPayBundle\Entity\CardRegistrationResult;
use Teknoo\MangoPayBundle\Entity\CardRegistrationSession;
use Teknoo\MangoPayBundle\Entity\Interfaces\User\UserInterface;
use Teknoo\MangoPayBundle\Event\MangoPayEvents;
use Teknoo\MangoPayBundle\Event\RegistrationEvent;
use Teknoo\MangoPayBundle\Exception\BadMangoReturnException;
use Teknoo\MangoPayBundle\Service\Interfaces\StorageServiceInterface;

/**
 * Class CardRegistrationService.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class CardRegistrationService
{
    const SESSION_PREFIX = 'MANGO_CARD_REGISTRATION';

    /**
     * @var ApiCardRegistrations
     */
    protected $mangoApiCardRegistration;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $returnRouteName;

    /**
     * @var StorageServiceInterface
     */
    protected $storageService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatched;

    /**
     * @param ApiCardRegistrations $mangoApiCardRegistration
     * @param Router               $router
     * @param $returnRouteName
     * @param StorageServiceInterface  $storageService
     * @param EventDispatcherInterface $eventDispatched
     */
    public function __construct(
        ApiCardRegistrations $mangoApiCardRegistration,
        Router $router,
        $returnRouteName,
        StorageServiceInterface $storageService,
        EventDispatcherInterface $eventDispatched
    ) {
        $this->mangoApiCardRegistration = $mangoApiCardRegistration;
        $this->router = $router;
        $this->returnRouteName = $returnRouteName;
        $this->storageService = $storageService;
        $this->eventDispatched = $eventDispatched;
    }

    /**
     * Perform a request to mango pay to get data needed to populate the card registration form.
     *
     * @param UserInterface $user
     * @param string        $cardType
     *
     * @return CardRegistration
     */
    protected function getCardRegistrationData(UserInterface $user, $cardType = 'CB_VISA_MASTERCARD')
    {
        $cardRegistrationRequest = new CardRegistration();
        $cardRegistrationRequest->Currency = 'EUR';
        $cardRegistrationRequest->CardType = $cardType;
        $cardRegistrationRequest->UserId = $user->getMangoPayId();

        return $this->mangoApiCardRegistration->Create($cardRegistrationRequest);
    }

    /**
     * To return a registration session container from the storage.
     *
     * @param string $sessionId
     *
     * @return CardRegistrationSession
     *
     * @throws \RuntimeException
     */
    public function getRegistrationSessionFromId($sessionId)
    {
        if ($this->storageService->has(self::SESSION_PREFIX.$sessionId)) {
            return $this->storageService->get(self::SESSION_PREFIX.$sessionId);
        }

        throw new \RuntimeException('Error, registration session is invalid');
    }

    /**
     * To return the card registration object from mango api.
     *
     * @param int $id
     *
     * @return CardRegistration
     *
     * @throws BadMangoReturnException
     */
    public function getCardRegistrationFromMango($id)
    {
        $cardRegistration = $this->mangoApiCardRegistration->Get($id);

        if (!$cardRegistration instanceof CardRegistration) {
            throw new BadMangoReturnException('Error, card registration not found in mango service');
        }

        return $cardRegistration;
    }

    /**
     * To prepare the request, call mangopay to retrieve url and token to submit the card registration form
     * and register registration session's data in the storage, then prepare the url of return.
     *
     *
     * @param UserInterface                $user
     * @param string                       $cardType
     * @param null|CardRegistrationSession $session
     *
     * @return CardRegistrationResult
     */
    public function prepare(UserInterface $user, CardRegistrationSession $session, $cardType = 'CB_VISA_MASTERCARD')
    {
        if (empty($user->getMangoPayId())) {
            throw new BadMangoEntityException('Error, the user has not a valid mango pay id');
        }

        $apiResult = $this->getCardRegistrationData($user, $cardType);

        $session->setCardRegistrationId($apiResult->Id)->setUser($user);
        $sessionId = $session->getSessionId();
        $this->storageService->set(self::SESSION_PREFIX.$sessionId, $session);

        $result = new CardRegistrationResult($user);
        $result->setAccessKeyRef($apiResult->AccessKey);
        $result->setData($apiResult->PreregistrationData);
        $result->setCardRegistrationUrl($apiResult->CardRegistrationURL);
        $result->setId($apiResult->Id);
        $result->setReturnUrl(
            $this->router->generate(
                $this->returnRouteName,
                ['registrationSessionId' => $sessionId],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );

        return $result;
    }

    /**
     * @param string   $sessionId
     * @param string   $data
     * @param Response $response
     *
     * @return self
     *
     * @throws \RuntimeException
     * @throws BadMangoReturnException
     */
    public function processMangoPayValidReturn($sessionId, $data, Response $response)
    {
        $registrationSession = $this->getRegistrationSessionFromId($sessionId);

        $cardRegistration = $this->getCardRegistrationFromMango($registrationSession->getCardRegistrationId());

        try {
            $cardRegistration->RegistrationData = 'data='.$data;
            $cardRegistration = $this->mangoApiCardRegistration->Update($cardRegistration);

            if (!empty($cardRegistration->CardId) && 'VALIDATED' == $cardRegistration->Status) {
                $this->eventDispatched->dispatch(
                    MangoPayEvents::CARD_REGISTRATION_VALIDATED,
                    new RegistrationEvent(
                        $registrationSession,
                        $cardRegistration,
                        $response
                    )
                );

                return $this;
            }
        } catch (\Exception $e) {
            //todo
        }

        $this->eventDispatched->dispatch(
            MangoPayEvents::CARD_REGISTRATION_ERROR_IN_VALIDATING,
            new RegistrationEvent(
                $registrationSession,
                $cardRegistration,
                $response
            )
        );

        return $this;
    }

    /**
     * @param string   $sessionId
     * @param string   $errorCode
     * @param Response $response
     *
     * @return self
     *
     * @throws \RuntimeException
     * @throws BadMangoReturnException
     */
    public function processMangoPayError($sessionId, $errorCode, Response $response)
    {
        $registrationSession = $this->getRegistrationSessionFromId($sessionId);

        $cardRegistration = $this->getCardRegistrationFromMango($registrationSession->getCardRegistrationId());
        $cardRegistration->RegistrationData = 'errorCode='.$errorCode;

        try {
            $cardRegistration = $this->mangoApiCardRegistration->Update($cardRegistration);

            $this->eventDispatched->dispatch(
                MangoPayEvents::CARD_REGISTRATION_ERROR,
                new RegistrationEvent(
                    $registrationSession,
                    $cardRegistration,
                    $response
                )
            );

            return $this;
        } catch (\Exception $e) {
            //todo
        }

        $this->eventDispatched->dispatch(
            MangoPayEvents::CARD_REGISTRATION_ERROR,
            new RegistrationEvent(
                $registrationSession,
                $cardRegistration,
                $response
            )
        );

        return $this;
    }
}
