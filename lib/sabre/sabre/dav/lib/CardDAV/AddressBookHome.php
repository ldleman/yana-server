<?php

namespace Sabre\CardDAV;

use Sabre\DAV;
use Sabre\DAV\MkCol;
use Sabre\DAVACL;
use Sabre\Uri;

/**
 * AddressBook Home class
 *
 * This collection contains a list of addressbooks associated with one user.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class AddressBookHome extends DAV\Collection implements DAV\IExtendedCollection, DAVACL\IACL {

    /**
     * Principal uri
     *
     * @var array
     */
    protected $principalUri;

    /**
     * carddavBackend
     *
     * @var Backend\BackendInterface
     */
    protected $carddavBackend;

    /**
     * Constructor
     *
     * @param Backend\BackendInterface $carddavBackend
     * @param string $principalUri
     */
    function __construct(Backend\BackendInterface $carddavBackend, $principalUri) {

        $this->carddavBackend = $carddavBackend;
        $this->principalUri = $principalUri;

    }

    /**
     * Returns the name of this object
     *
     * @return string
     */
    function getName() {

        list(, $name) = Uri\split($this->principalUri);
        return $name;

    }

    /**
     * Updates the name of this object
     *
     * @param string $name
     * @return void
     */
    function setName($name) {

        throw new DAV\Exception\MethodNotAllowed();

    }

    /**
     * Deletes this object
     *
     * @return void
     */
    function delete() {

        throw new DAV\Exception\MethodNotAllowed();

    }

    /**
     * Returns the last modification date
     *
     * @return int
     */
    function getLastModified() {

        return null;

    }

    /**
     * Creates a new file under this object.
     *
     * This is currently not allowed
     *
     * @param string $filename
     * @param resource $data
     * @return void
     */
    function createFile($filename, $data = null) {

        throw new DAV\Exception\MethodNotAllowed('Creating new files in this collection is not supported');

    }

    /**
     * Creates a new directory under this object.
     *
     * This is currently not allowed.
     *
     * @param string $filename
     * @return void
     */
    function createDirectory($filename) {

        throw new DAV\Exception\MethodNotAllowed('Creating new collections in this collection is not supported');

    }

    /**
     * Returns a single addressbook, by name
     *
     * @param string $name
     * @todo needs optimizing
     * @return \AddressBook
     */
    function getChild($name) {

        foreach ($this->getChildren() as $child) {
            if ($name == $child->getName())
                return $child;

        }
        throw new DAV\Exception\NotFound('Addressbook with name \'' . $name . '\' could not be found');

    }

    /**
     * Returns a list of addressbooks
     *
     * @return array
     */
    function getChildren() {

        $addressbooks = $this->carddavBackend->getAddressBooksForUser($this->principalUri);
        $objs = [];
        foreach ($addressbooks as $addressbook) {
            $objs[] = new AddressBook($this->carddavBackend, $addressbook);
        }
        return $objs;

    }

    /**
     * Creates a new address book.
     *
     * @param string $name
     * @param MkCol $mkCol
     * @throws DAV\Exception\InvalidResourceType
     * @return void
     */
    function createExtendedCollection($name, MkCol $mkCol) {

        if (!$mkCol->hasResourceType('{' . Plugin::NS_CARDDAV . '}addressbook')) {
            throw new DAV\Exception\InvalidResourceType('Unknown resourceType for this collection');
        }
        $properties = $mkCol->getRemainingValues();
        $mkCol->setRemainingResultCode(201);
        $this->carddavBackend->createAddressBook($this->principalUri, $name, $properties);

    }

    /**
     * Returns the owner principal
     *
     * This must be a url to a principal, or null if there's no owner
     *
     * @return string|null
     */
    function getOwner() {

        return $this->principalUri;

    }

    /**
     * Returns a group principal
     *
     * This must be a url to a principal, or null if there's no owner
     *
     * @return string|null
     */
    function getGroup() {

        return null;

    }

    /**
     * Returns a list of ACE's for this node.
     *
     * Each ACE has the following properties:
     *   * 'privilege', a string such as {DAV:}read or {DAV:}write. These are
     *     currently the only supported privileges
     *   * 'principal', a url to the principal who owns the node
     *   * 'protected' (optional), indicating that this ACE is not allowed to
     *      be updated.
     *
     * @return array
     */
    function getACL() {

        return [
            [
                'privilege' => '{DAV:}read',
                'principal' => $this->principalUri,
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}write',
                'principal' => $this->principalUri,
                'protected' => true,
            ],
        ];

    }

    /**
     * Updates the ACL
     *
     * This method will receive a list of new ACE's.
     *
     * @param array $acl
     * @return void
     */
    function setACL(array $acl) {

        throw new DAV\Exception\MethodNotAllowed('Changing ACL is not yet supported');

    }

    /**
     * Returns the list of supported privileges for this node.
     *
     * The returned data structure is a list of nested privileges.
     * See Sabre\DAVACL\Plugin::getDefaultSupportedPrivilegeSet for a simple
     * standard structure.
     *
     * If null is returned from this method, the default privilege set is used,
     * which is fine for most common usecases.
     *
     * @return array|null
     */
    function getSupportedPrivilegeSet() {

        return null;

    }

}
