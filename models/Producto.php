<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto".
 *
 * @property int $id_Producto
 * @property string $nombre
 * @property string|null $descrip_producto
 * @property bool $est_producto
 * @property int $stock
 * @property float $precio_costo
 * @property float $precio_venta
 * @property int|null $fk_categoria
 * @property string|null $imagen
 *
 * @property FacturaHasProducto[] $facturaHasProductos
 * @property Categoria $fkCategoria
 */
class Producto extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descrip_producto', 'fk_categoria', 'imagen'], 'default', 'value' => null],
            [['est_producto'], 'default', 'value' => 1],
            [['nombre', 'stock', 'precio_costo', 'precio_venta'], 'required'],
            [['est_producto'], 'boolean'],
            [['stock', 'fk_categoria'], 'integer'],
            [['precio_costo', 'precio_venta'], 'number'],
            [['nombre'], 'string', 'max' => 60],
            [['descrip_producto'], 'string', 'max' => 100],
            [['imagen'], 'string', 'max' => 255],
            [['fk_categoria'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['fk_categoria' => 'id_categoria']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_Producto' => 'Id Producto',
            'nombre' => 'Nombre',
            'descrip_producto' => 'Descrip Producto',
            'est_producto' => 'Est Producto',
            'stock' => 'Stock',
            'precio_costo' => 'Precio Costo',
            'precio_venta' => 'Precio Venta',
            'fk_categoria' => 'Fk Categoria',
            'imagen' => 'Imagen',
        ];
    }

    /**
     * Gets query for [[FacturaHasProductos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacturaHasProductos()
    {
        return $this->hasMany(FacturaHasProducto::class, ['id_producto' => 'id_Producto']);
    }

    /**
     * Gets query for [[FkCategoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFkCategoria()
    {
        return $this->hasOne(Categoria::class, ['id_categoria' => 'fk_categoria']);
    }

}
