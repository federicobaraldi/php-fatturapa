<?php
/**
 * Created by PhpStorm.
 * User: robertogallea
 * Date: 6/13/15
 * Time: 11:35 AM
 */

namespace Robertogallea\FatturaPA\Services\FatturaPAToCsv;

use Robertogallea\FatturaPA\FatturaPA;
use Robertogallea\FatturaPA\Model\Common\DatiAnagrafici;
use Robertogallea\FatturaPA\Services\FatturaPAToCsv;


class CsvRiepilogoType extends FatturaPAToCsv
{

    public function __construct($filenames = [])
    {

        $this->fatture = $filenames;

    }

    protected function setHeaders()
    {

        $headers = [

            'File',
            'Partita Iva',
            'Denominazione',

            'Data ricezione',
            'Data fattura',
            'Numero',
            'Causale',
            'Importo documento',
            'Arrotondamento documento',

            'Aliquota',
            'Imponibile',
            'Imposta',
            'Importo',
            'Arrotondamento',

        ];

        return implode($this->separator, $headers) . $this->breakline;


    }


    protected function addFatturaRows($fattura, $csvContent)
    {
        $fatturaRowsHeader = $this->getFatturaRowsHeader($fattura);

        $fatturaBodies = $fattura->getFatturaElettronicaBody();

        foreach ($fatturaBodies as $fatturaBody) {
            $fatturaRowsBodyGenerali = $this->getFatturaRowsBodyGenerali($fatturaBody);

            $fatturaRowsBodyDatiRiepilogo = $fatturaBody->getDatiBeniServizi()->getDatiRiepilogo();

            foreach ($fatturaRowsBodyDatiRiepilogo as $fatturaBodyDatoRiepilogo) {

                $fatturaRowsBodyDatoRiepilogo = $this->getFatturaRowsBodyDatoRiepilogo($fatturaBodyDatoRiepilogo);

                $rowArray = array_merge(
                    ['File' => $this->currentFilename],
                    $fatturaRowsHeader,
                    $fatturaRowsBodyGenerali,
                    $fatturaRowsBodyDatoRiepilogo
                );

                $csvContent .= implode($this->separator, array_values($rowArray)) . $this->breakline;
            }
        }

        return $csvContent;
    }


    protected function getFatturaRowsBodyDatoRiepilogo($fatturaBodyDatoRiepilogo)
    {


        $imponibile = $fatturaBodyDatoRiepilogo->getImponibileImporto();
        $imposta = $fatturaBodyDatoRiepilogo->getImposta();
        return [
            'Aliquota' => $fatturaBodyDatoRiepilogo->getAliquotaIVA(),
            'Imponibile' => $imponibile,
            'Imposta' => $imposta,
            'Importo' => number_format((float)$imponibile + (float)$imposta,2,'.',''),
            'Arrotondamento' => $fatturaBodyDatoRiepilogo->Arrotondamento(),
        ];
    }

}
