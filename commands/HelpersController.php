<?php

namespace tunecino\builder\commands;
 
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
 

class HelpersController extends Controller
{

    public function beforeAction($action) {
        echo 'Helper command provided by Schema Builder extension'. PHP_EOL. PHP_EOL;
        return parent::beforeAction($action);
    }


    public function actionRemoveDirectory($path)
    {
        $path = Yii::getAlias($path);
    	FileHelper::removeDirectory($path);
		echo '"' . $path .'" has been removed.'. PHP_EOL;
    }


    public function actionDropAllTables($db)
    {
        $db = Yii::$app->get($db);
        $tables = $db->schema->getTableNames();

        if (!$tables) {
            echo 'nothing to drop.'. PHP_EOL;
            return 0;
        }

        foreach ($tables as $table) {
            $db->createCommand()->dropTable($table)->execute();
            echo 'dropped table "' . $table . '".'. PHP_EOL;
        }

        echo PHP_EOL. 'database should be empty now.' . PHP_EOL;
    }

    public function actionDropTables($db, $tables = null)
    {
        $db = Yii::$app->get($db);
        $all_tables = $db->schema->getTableNames();
        $tablesArr = explode(',', $tables);
        print_r($tablesArr);
        if (!$all_tables || empty($tablesArr)) {
            echo 'nothing to drop.'. PHP_EOL;
            return 0;
        }

        Yii::$app->db->createCommand("SET foreign_key_checks = 0")->execute();
        foreach ($all_tables as $table) {
            if(in_array($table, $tablesArr)) {
                $db->createCommand()->dropTable($table)->execute();
                echo 'dropped table "' . $table . '".'. PHP_EOL;
            }
        }
        Yii::$app->db->createCommand("SET foreign_key_checks = 1")->execute();

        echo PHP_EOL. 'selected tables should be dropped now.' . PHP_EOL;
    }


    public function actionAddRestRulesToFile($file, $controllers)
    {
        $controllers = explode(',', $controllers);
        $file = Yii::getAlias($file);
        if (substr($file, -4) !== '.php' || file_exists($file) === false) return 0;

        $content = '<?php' . PHP_EOL . 'return [';
        foreach ($controllers as $controller) {
            $content .=  PHP_EOL . "   ['class' => 'yii\\rest\\UrlRule', 'controller' => '$controller']," . PHP_EOL;
        }
        $content .= '];';

        if (file_put_contents($file, $content) === false) return 0;
        echo '"' . $file .'" has been created.'. PHP_EOL;
    }
}