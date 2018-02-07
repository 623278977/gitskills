<?php

namespace App\Models;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Structure;
use Passbook\Type\EventTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Passbook extends Model
{
    static function createPassbook($subject,$address,$begin_time){

		$loader = require_once __DIR__ . "/../Http/Libs/passbook/vendor/autoload.php";

		$loader->add('Passbook\\', '../src/Passbook');
		$loader->add('Passbook\Tests', 'Passbook/Tests');

		// Set these constants with your values
		define('P12_FILE', dirname(__FILE__).'/../Http/Libs/passbook/tests/path/to/p12/Certificate.p12');
		define('P12_PASSWORD',config('app.P12_PASSWORD'));
		define('WWDR_FILE', dirname(__FILE__).'/../Http/Libs/passbook/tests/path/to/AppleWWDRCA.pem');
		define('PASS_TYPE_IDENTIFIER', config('app.PASS_TYPE_IDENTIFIER'));
		define('TEAM_IDENTIFIER', config('app.TEAM_IDENTIFIER'));
		define('ORGANIZATION_NAME',  config('app.ORGANIZATION_NAME'));
		define('OUTPUT_PATH', dirname(__FILE__).'/../../public/attached/file/pkass');
		define('ICON_FILE', dirname(__FILE__).'/../Http/Libs/passbook/tests/path/to/icon.png');
		$name=date('YmdHis',time()).unique_id();

		$pass = new EventTicket($name, $subject);
		$pass->setBackgroundColor('rgb(255, 255, 255)');
		$pass->setLogoText('Apple Inc.');

// Create pass structure
		$structure = new Structure();

// Add primary field
		$primary = new Field('event', $subject);
		$primary->setLabel('Event');
		$structure->addPrimaryField($primary);

// Add secondary field
		$secondary = new Field('location',$address);
		$secondary->setLabel('Location');
		$structure->addSecondaryField($secondary);

// Add auxiliary field
		$auxiliary = new Field('datetime', $begin_time);
		$auxiliary->setLabel('Date & Time');
		$structure->addAuxiliaryField($auxiliary);

// Add icon image
		$icon = new Image(ICON_FILE,'icon');
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
		return config('app.base_url').'/attached/file/pkass/'.$name.'.pkpass';
	}
}
