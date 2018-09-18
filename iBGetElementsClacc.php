<?php

/**
 * Class iBGetElementsClacc
 *
 * $test = new iBGetElementsClacc();
 * $test->cleanCache = true; // Выключаем кэш
 * $test->arFilter = array("IBLOCK_ID" => 2);
 * $el = $test->get_elements();
 * echo "<pre>";
 * print_r($el);
 * echo "<pre>";
 *
 *
 */
class iBGetElementsClacc
{

    /**
     * @var $arOrder array
     * @var $arFilter array
     * @var $group array
     * @var $limit array
     * @var $arSelect array
     * @var $useCache bool
     * @var $cache_time int
     * @var $cleanCache bool
     */
    public $arOrder = Array();
    public $arFilter;
    public $group = false;
    public $limit = false;
    public $arSelect = Array("ID", "IBLOCK_ID", "NAME");
    public $useCache = false;
    public $cache_time = 36000;
    public $cleanCache = false;

    /**
     * @return array
     */
    public  function get_elements () {
        $cache_id = md5(serialize($this->arFilter));
        $cache_dir = "/class_getlist";

        $obCache = new CPHPCache;
        if ($this->cleanCache) $obCache->CleanDir($cache_dir);
        if($obCache->InitCache($this->cache_time, $cache_id, $cache_dir))
        {
            $arElements = $obCache->GetVars();
        }
        elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache())
        {
            $arElements = array();

            $rsElements = CIBlockElement::GetList($this->arOrder, $this->arFilter, $this->group, $this->limit, $this->arSelect);

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_dir);
            while($arElement = $rsElements->Fetch())
            {
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arElement["IBLOCK_ID"]);
                $arElements[] = $arElement;
            }
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            $obCache->EndDataCache($arElements);
        }
        else
        {
            $arElements = array();
        }

        return $arElements;
    }
}