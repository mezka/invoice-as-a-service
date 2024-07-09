<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\PDF;
use Smalot\PdfParser\Parser;

class TestPDF extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testPDFBuildDefaultView()

    {

        $data = [
            "id" => "43",
            "currency" => "€",
            "lang" => "en",
            "date" => 1520852472,
            "due_date" => 1521457272,
            "paid" => false,
            "payment_link" => "https://screeb.app/user/invoices/42/pay",
            "decimals" => 2,
            "notes" => "Lorem ipsum dolor sit amet.",
            "items" => [
                [
                    "title" => "'Growth' plan Screeb.app",
                    "description" => "1 year subscription",
                    "price" => 42,
                    "quantity" => 1,
                    "tax" => 20
                ]
            ],
            "customer" => [
                "summary" => "John Doe",
                "address_line_1" => "Baxter Building, 42nd street, Madison Avenue",
                "address_line_2" => "Manhattan, NY, 11234",
                "address_line_3" => "United States",
                "address_line_4" => "Earth",
                "phone" => "1-888-548-0034",
                "email" => "john@gmail.com"
            ],
            "company" => [
                "summary" => "Screeb",
                "address_line_1" => "123, place de Bretagne",
                "address_line_2" => "44000 Nantes",
                "address_line_3" => "France",
                "address_line_4" => "Earth",
                "phone" => "1-888-548-0034",
                "email" => "billing@screeb.app",
                "logo_url" => "https://raw.githubusercontent.com/samber/invoice-as-a-service/master/screeb-logo.png",
                "other" => [
                    "EMEA office",
                    [
                        "title" => "Business hours",
                        "content" => "9am - 6pm"
                    ]
                ]
            ]
        ];

        $pdf = new PDF(
            $data['id'],
            $data['currency'],
            $data['lang'],
            $data['date'],
            $data['due_date'],
            $data['paid'],
            $data['payment_link'],
            $data['notes'],
            $data['items'],
            $data['customer'],
            $data['company'],
            $data['sub_total'],
            $data['tax_total'],
            $data['total']
        );

        $pdfContent = $pdf->build('default');
        file_put_contents('php://memory', $pdfContent);
        
        $pdfParser = Parser();
        $pdf = $pdfParser->parseContent($pdfContent);

        $pages = $pdf->getPages();

        $this->assertEquals(1, count($pages), 'PDF should have 1 page');

        $text = $pages[0]->getText();
        $pageMetadata = $pages[0]->getDetails();

        print($pageMetadata);

        $this->assertStringContainsString('42', $text, 'PDF should contain text for invoice id "43"');
        $this->assertStringContainsString('€', $text, 'PDF should contain text for currency symbol "€"');
        $this->assertStringContainsString('12 Mar 2018', $text, 'PDF should contain text for invoice date "12 Mar 2018"');
        $this->assertStringContainsString('19 Mar 2018', $text, 'PDF should contain text for invoice due date "19 Mar 2018');
        $this->assertStringContainsString('https://screeb.app/user/invoices/42/pay', $text, 'PDF should contain text for payment link "https://screeb.app/user/invoices/42/pay"');
        $this->assertStringContainsString('Lorem ipsum dolor sit amet.', $text, 'PDF should contain text for notes "Lorem ipsum dolor sit amet."');
    }
}
