<?php
namespace Vspace\Optimization\DataProviders;

use Vspace\Optimization\DataProviders\Entities\OptionTable;

require_once __DIR__ . "/Entities/OptionTable.php";

class OptionProvider
{

    public const KEY_CODE     = 'ex_scripts_code';
    public const KEY_PLACE    = 'ex_scripts_place';
    public const KEY_DELAYED  = 'ex_scripts_delayed';
    public const KEY_TIME     = 'ex_scripts_time';
    public const KEY_CSS      = 'ex_scripts_css';

    public const KEY_CODE_MODIF = 'ex_scripts_code_modificate';

    public const KEY_PLACE_HEAD        = 'head';
    public const KEY_PLACE_BEGIN_BODY  = 'begin_body';
    public const KEY_PLACE_END_BODY    = 'end_body';

    /*
    *  Получаем список параметров в виде ключ => значение
    */
    public function getOptions()
    {
        $options  = [];

        $result = OptionTable::getList(array(
            'select'  => ['NAME', 'VALUE'],
            'filter'  => [
                '=MODULE_ID' => 'vspace.optimization'
            ]
        ));

        while ($row = $result->fetch()){
            $options[$row['NAME']] = $row['VALUE'];
        }

        return $options;
    }

}