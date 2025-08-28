<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categoria".
 *
 * @property int $id_categoria
 * @property string $nom_categoria
 * @property string $des_categoria
 * @property string|null $abreviatura
 * @property bool $estado
 *
 * @property Producto[] $productos
 */
class Categoria extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categoria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['abreviatura'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 1],
            [['nom_categoria', 'des_categoria'], 'required'],
            [['estado'], 'boolean'],
            [['nom_categoria'], 'string', 'max' => 30],
            [['des_categoria'], 'string', 'max' => 150],
            [['abreviatura'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_categoria' => 'Id Categoria',
            'nom_categoria' => 'Nom Categoria',
            'des_categoria' => 'Des Categoria',
            'abreviatura' => 'Abreviatura',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[Productos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Producto::class, ['fk_categoria' => 'id_categoria']);
    }

}
