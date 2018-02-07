<?php

/*
 * This file is part of the Passbook package.
 *
 * (c) Eymen Gunay <eymen@egunay.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$loader = require_once __DIR__ . "/../vendor/autoload.php";
$loader->add('Passbook\\', '../src/Passbook');
$loader->add('Passbook\Tests', 'Passbook/Tests');

use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Structure;
use Passbook\Type\EventTicket;

// Set these constants with your values
define('P12_FILE', dirname(__FILE__).'/path/to/p12/Certificate.p12');
define('P12_PASSWORD','tyrbl');
define('WWDR_FILE', dirname(__FILE__).'/path/to/AppleWWDRCA.pem');
define('PASS_TYPE_IDENTIFIER', 'pass.com.wujiesq.mypassbook');
define('TEAM_IDENTIFIER', 'WAMV66QUDM');
define('ORGANIZATION_NAME', 'Your Organization Name');
define('OUTPUT_PATH', dirname(__FILE__).'/path/to/output/path');
define('ICON_FILE', dirname(__FILE__).'/path/to/icon.png');

// Create an event ticket
$pass = new EventTicket("1234567890", "The Beat Goes On");
$pass->setBackgroundColor('rgb(60, 65, 76)');
$pass->setLogoText('Apple Inc.');

// Create pass structure
$structure = new Structure();

// Add primary field
$primary = new Field('event', 'The Beat Goes On');
$primary->setLabel('Event');
$structure->addPrimaryField($primary);

// Add secondary field
$secondary = new Field('location', 'Moscone West');
$secondary->setLabel('Location');
$structure->addSecondaryField($secondary);

// Add auxiliary field
$auxiliary = new Field('datetime', '2013-04-15 @10:25');
$auxiliary->setLabel('Date & Time');
$structure->addAuxiliaryField($auxiliary);

// Add icon image
$icon = new Image(ICON_FILE, 'icon');
$pass->addImage($icon);

// Set pass structure
$pass->setStructure($structure);

// Add barcode
$barcode = new Barcode(Barcode::TYPE_QR, 'barcodeMessage');
$pass->setBarcode($barcode);

// Create pass factory instance
$factory = new PassFactory(PASS_TYPE_IDENTIFIER, TEAM_IDENTIFIER, ORGANIZATION_NAME, P12_FILE, P12_PASSWORD, WWDR_FILE);
$factory->setOutputPath(OUTPUT_PATH);
$factory->package($pass);