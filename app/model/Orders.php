<?php

namespace App\Model;


use Doctrine\ORM\EntityManager;
use Nette\Object;
use TCPDF;

class Orders extends Object
{
    /** @var EntityManager */
    private $em;

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param int|Order $order
     * @param int $status
     */
    function changeStatus($order, $status)
    {
        if (is_object($order)) {
            $order->setStatus($status);
        } else {
            $this->em->find(Order::class, $order)->setStatus($status);
        }

        $this->em->flush();
    }

    function invoice(Order $order, $toFile = false)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'windows-1250', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 049');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->SetFont('freesans', '', 12, '', 'false');

        $pdf->AddPage();

        $total_price = $order->getPricePerUnit() * $order->getAmount();
        $html = "
<h1>Faktura F{$order->getNum()}</h1>
<h3>Dodavatel</h3>
<table>
    <tr>
        <td>" . str_replace("\n", "<br>", Settings::get('supply.address')) . "</td>
        <td></td>
    </tr>
    <tr>
        <td>IČ:</td>
        <td>" . Settings::get('supply.ic') . "</td>
    </tr>
    <tr>
        <td>DIČ:</td>
        <td>" . Settings::get('supply.dic') . "</td>
    </tr>
</table>

<h3>Odběratel</h3>
<table>
    <tr>
        <td>{$order->getName()}</td>
        <td></td>
    </tr>
    <tr>
        <td>" . str_replace("\n", "<br>", $order->getInvoiceAddress()) . "</td>
        <td></td>
    </tr>
    <tr>
        <td>IČ:</td>
        <td>{$order->getIc()}</td>
    </tr>
    <tr>
        <td>DIČ:</td>
        <td>{$order->getDic()}</td>
    </tr>
</table>

<h3>Položky</h3>
<table>
    <tr>
        <th><strong>Označení dodávky</strong></th>
        <th><strong>Počet m. j.</strong></th>
        <th><strong>Cena za m.j.</strong></th>
        <th><strong>Celkem</strong></th>
    </tr>
    <tr>
        <td>{$order->getType(true)}</td>
        <td>{$order->getAmount()}</td>
        <td>{$order->getPricePerUnit()},00</td>
        <td>{$total_price},00</td>
    </tr>
</table>

<h3>Celkem k úhradě: {$total_price},00 Kč</h3>
        ";
        $pdf->writeHTML($html);

        $filename = $order->getInvoiceFileName();
        if ($toFile) {
            $pathToFile = WWW_DIR . '/../temp/' . $filename;
            if (file_exists($pathToFile)) {
                unlink($pathToFile);

                $pdf->Output($pathToFile, 'F');
            }
        } else {
            $pdf->Output($filename, 'I');
        }
    }
}