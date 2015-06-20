<?php

use App\Facades\OpenFDA;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class OpenFDATest extends TestCase
{
	public static function setUpBeforeClass()
	{
		// Create a mock and queue two responses.
		$mock = new MockHandler([
			new Response(200, ['X-Foo' => 'Bar'], '{"meta":{"disclaimer":"openFDA is a beta research project and not for clinical use. While we make every effort to ensure that data is accurate, you should assume all results are unvalidated.","license":"http:\/\/open.fda.gov\/license","last_updated":"2015-05-31","results":{"skip":0,"limit":1,"total":145}},"results":[{"set_id":"0000076a-fc39-4208-ace8-6c2cb367904f","indications_and_usage":["Uses Multi-purpose mineral powder provides broad-spectrum SPF 46 PA+++ protection. Leaves the skin flawless and protected."],"keep_out_of_reach_of_children":["Keep out of reach of children If product is swallowed, get medical help or contact a Poison Control Center right away"],"dosage_and_administration":["Directions Protection Naturelle SPF 46 PA+++ Powder can be used on clean skin or over makeup. Shake lightly to activate the flow of powder. Sweep the brush all over the face to evenly distribute powder for immediate UVA\/UVB protection."],"purpose":["Purpose Sunscreen"],"version":"4","id":"f229e866-5775-4e42-a316-8480dd92fec6","package_label_principal_display_panel":["CHANTECAILLE Protection Naturelle SPF 46 PA+++ Powder NET WT. 0.088 OZ. 2.5g e","CHANTECAILLE PROTECTION NATURELLE BRONZE SPF 46 .088OZ\/2.5g (42893-030-00) CHANTECAILLE PROTECTION NATURELLE SPF 46"],"active_ingredient":["BRONZE ACTIVE INGREDIENTS: TITANIUM DIOXIDE 2 %, ETHYLHEXYL METHOXYCINNAMATE 7%, ZINC OXIDE 24.5%"],"inactive_ingredient":["INGREDIENTS: TALC, POLYMETHYL METHACRYLATE, VINYL DIMETHICONE\/METHICONE SILSESQUIOXANE CROSSPOLYMER, CALCIUM SILICATE, TRIETHYLHEXANOIN, ALUMINUM HYDROXIDE, LAUROYL LYSINE, METHICONE, PHENOXYETHANOL, DIMETHICONE, ALUMINUM DIMYRISTATE, HYDROXYAPATITIE [+\/-: MICA (CI77019), IRON OXIDES (CI 77491\/CI 77492\/CI 77499)]"],"@epoch":1421884901.5412,"effective_time":"20150109","openfda":{"spl_id":["50de8449-69cf-4593-bdac-4aae7e7b4b7b"],"product_ndc":["42893-030"],"is_original_packager":[true],"route":["TOPICAL"],"substance_name":["OCTINOXATE","TITANIUM DIOXIDE","ZINC OXIDE"],"spl_set_id":["0000076a-fc39-4208-ace8-6c2cb367904f"],"package_ndc":["42893-030-00"],"product_type":["HUMAN OTC DRUG"],"generic_name":["TITANIUM DIOXIDE, OCTINOXATE, ZINC OXIDE"],"manufacturer_name":["Chantecaille Beaute Inc"],"brand_name":["CHANTECAILLE PROTECTION NATURELLE BRONZE SPF 46"],"application_number":["part352"]},"spl_product_data_elements":["CHANTECAILLE PROTECTION NATURELLE BRONZE SPF 46 TITANIUM DIOXIDE, OCTINOXATE, ZINC OXIDE TITANIUM DIOXIDE TITANIUM DIOXIDE OCTINOXATE OCTINOXATE ZINC OXIDE ZINC OXIDE TALC CALCIUM SILICATE TRIETHYLHEXANOIN ALUMINUM HYDROXIDE LAUROYL LYSINE PHENOXYETHANOL DIMETHICONE ALUMINUM DIMYRISTATE MICA FERRIC OXIDE RED"],"when_using":["When using this product keep out of eyes. Rinse with water to remove."],"warnings":["Warnings For external use only."]}]}'),
			new Response(404, ['Content-Length' => 0])
		]);

		$handler = HandlerStack::create($mock);
		$client = new Client(['handler' => $handler]);

		app()->instance('Guzzle', $client);
	}

    public function testGetDrugInfo()
    {
		$fda_info = json_decode(OpenFDA::getDrugInfo('42893-030'), true);
        $this->assertEquals('TITANIUM DIOXIDE, OCTINOXATE, ZINC OXIDE', $fda_info['results'][0]['openfda']['generic_name'][0]);
    }

    public function testBadDrugInfo()
	{
		$response = OpenFDA::getDrugInfo('foo');
		$this->assertEquals('Not Found', $response->getReasonPhrase());
    }
}
