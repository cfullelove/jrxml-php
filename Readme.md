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

$pdf = $report->renderReport( new JasperReport\OutputAdapter\FPDFOutputAdapter(), array() );

file_put_contents( 'test.pdf', $pdf->output( 'test.pdf' ) );

```
