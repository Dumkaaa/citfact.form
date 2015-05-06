<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<div class="form-group">
    <label><?= $arResult['LABEL'] ?></label>
    <? foreach ($arResult['VALUE_LIST'] as $value): ?>
        <? $checked = ($arResult['MULTIPLE'] == 'Y')
            ? (in_array($value['ID'], $arResult['VALUE'])) ? 'checked="checked"' : ''
            : ($value['ID'] == $arResult['VALUE']) ? 'checked="checked"' : '';
        ?>
        <input type="checkbox" name="<?= $arResult['NAME'] ?>"
               value="<?= $value['ID'] ?>" <?= $checked ?>/> <?= $value['VALUE'] ?>
    <? endforeach; ?>
</div>