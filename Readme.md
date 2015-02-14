#Readme

## Usage

```php
require( "vendor/autoload.php" );

$report = new JasperReport\JasperReport( __DIR__ . "/redbook_roster_published.jrxml" );

$datasource = new JasperReport\Datasource\MysqlDatasource(
	'localhost',
	'user',
	'pass',
	'db',
	3306
);

$params = array(
	'param1' => "value1",
	'param2' => "value2"
);

$pdf = $report->renderReport(
	new JasperReport\OutputAdapter\FPDFOutputAdapter(),
	$datasource, 
	$params
);

file_put_contents( 'filename.pdf', $pdf->output() );

```
