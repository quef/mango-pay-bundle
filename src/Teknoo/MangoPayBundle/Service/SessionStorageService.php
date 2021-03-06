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

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Teknoo\MangoPayBundle\Service\Interfaces\StorageServiceInterface;

/**
 * Class SessionStorageService.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/mangopay-bundle Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class SessionStorageService implements StorageServiceInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Gets the service container parameters.
     *
     * @return array An array of parameters
     *
     * @api
     */
    public function all()
    {
        return $this->session->all();
    }

    /**
     * Gets a service container parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     *
     * @throws \LogicException if the parameter is not defined
     *
     * @api
     */
    public function get($name)
    {
        return $this->session->get($name);
    }

    /**
     * Sets a service container parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @throws \LogicException if the parameter can not be set
     *
     * @return self
     *
     * @api
     */
    public function set($name, $value)
    {
        $this->session->set($name, $value);

        return $this;
    }

    /**
     * Returns true if a parameter name is defined.
     *
     * @param string $name The parameter name
     *
     * @return bool true if the parameter name is defined, false otherwise
     *
     * @api
     */
    public function has($name)
    {
        return $this->session->has($name);
    }
}
