<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\PDF;
use Smalot\PdfParser\Parser;

class PDFTest extends TestCase
{
    /**
     * Test for the App\Helpers\PDF class.
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
                    "price" => 12,
                    "quantity" => 1,
                    "tax" => 20,
                    "total" => 14.4
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
            44,
            8.8,
            50.8
        );

        $pdfContent = $pdf->build('default');
        
        $pdfParser = new Parser();
        $parsedPdf = $pdfParser->parseContent($pdfContent);

        $pages = $parsedPdf->getPages();
        $this->assertEquals(1, count($pages), 'PDF should have 1 page');

        $text = utf8_decode($parsedPdf->getText());

        $this->assertStringContainsString('43', $text, 'Should contain text for invoice id: "43"');
        $this->assertStringContainsString('€', $text, 'Should contain text for currency symbol: "€"');
        $this->assertStringContainsString('12 Mar 2018', $text, 'Should contain text for invoice date "12 Mar 2018"');
        $this->assertStringContainsString('19 Mar 2018', $text, 'Should contain text for invoice due date: "19 Mar 2018');
        $this->assertStringContainsString('https://screeb.app/user/invoices/42/pay', $text, 'Should contain text for payment link: "https://screeb.app/user/invoices/42/pay"');
        $this->assertStringContainsString('Lorem ipsum dolor sit amet.', $text, 'Should contain text for notes: "Lorem ipsum dolor sit amet."');

        $this->assertStringContainsString("'Growth' plan Screeb.app", $text, "Should contain text for item title: 'Growth' plan Screeb.app");
        $this->assertStringContainsString("1 year subscription", $text, 'Should contain text for item description: "1 year subscription"');
        $this->assertStringContainsString("12 €", $text, 'Should contain text for item price: "12 €"');
        $this->assertStringContainsString("1", $text, 'Should contain text for item quantity: "1"');
        $this->assertStringContainsString("20 %", $text, 'Should contain text for item tax percentage: "20 %"');

        $this->assertStringContainsString("John Doe", $text, 'Should contain text for customer summary: "John Doe"');
        $this->assertStringContainsString('Baxter Building, 42nd street, Madison Avenue', $text, 'Should contain customer address line 1');
        $this->assertStringContainsString('Manhattan, NY, 11234', $text, 'Should contain customer address line 2');
        $this->assertStringContainsString('United States', $text, 'Should contain customer address line 3');
        $this->assertStringContainsString('Earth', $text, 'Should contain customer address line 4');
        $this->assertStringContainsString('1-888-548-0034', $text, 'Should contain customer phone');
        $this->assertStringContainsString('john@gmail.com', $text, 'Should contain customer email');

        $this->assertStringContainsString('Screeb', $text, 'Should contain company summary');
        $this->assertStringContainsString('123, place de Bretagne', $text, 'Should contain company address line 1');
        $this->assertStringContainsString('44000 Nantes', $text, 'Should contain company address line 2');
        $this->assertStringContainsString('France', $text, 'Should contain company address line 3');
        $this->assertStringContainsString('Earth', $text, 'Should contain company address line 4');
        $this->assertStringContainsString('1-888-548-0034', $text, 'Should contain company phone');
        $this->assertStringContainsString('billing@screeb.app', $text, 'Should contain company email');

        $this->assertStringContainsString('EMEA office', $text, 'Should contain company other detail');
        $this->assertStringContainsString('Business hours', $text, 'Should contain company business hours title');
        $this->assertStringContainsString('9am - 6pm', $text, 'Should contain company business hours content');

        $this->assertStringContainsString('44 €', $text, 'Should contain text for subtotal 44 €"');
        $this->assertStringContainsString('8.8 €', $text, 'Should contain text for taxes 8.8 €"');
        $this->assertStringContainsString('50.8 €', $text, 'Should contain text for total 50.8 €"');
    }
}
