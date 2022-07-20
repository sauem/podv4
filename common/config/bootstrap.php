<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@mdm/admin/mail', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/mail');
Yii::setAlias('@mdm/admin/assets', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/assets');
Yii::setAlias('@mdm/admin/controllers', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/controllers');
Yii::setAlias('@mdm/admin/components', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/components');
Yii::setAlias('@mdm/admin/messages', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/messages');
Yii::setAlias('@mdm/admin/migrations', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/migrations');
Yii::setAlias('@mdm/admin', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin');
Yii::setAlias('@mdm/admin/*', dirname(dirname(__DIR__)) . '/vendor/mdmsoft/yii2-admin/*');
