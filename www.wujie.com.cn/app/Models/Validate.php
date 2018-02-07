<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validate extends Model
{
    /**
     * 验证值是否是指定类型
     *
     * @param $value    需要验证的值
     * @param $type     验证时，指定需要验证的类型
     *
     * @return bool
     */
    public static function validateValueTypes($value, $type)
    {
       return self::{'validate'.ucfirst($type)}($value);
    }

    /**
     * 验证值是否是整形值
     *
     * @param $value
     *
     * @return bool
     */
    public static function validateInt($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * 验证值是否是数字
     *
     * @param $value
     *
     * @return bool
     */
    public static function validateNumeric($value)
    {
        return is_numeric($value);
    }

    /**
     * 验证值是否设置或不为空
     *
     * @param $value
     *
     * @return bool
     */
    public static function validateIsSetOrNoEmpty($value)
    {
        return isset($value) || !empty($value);
    }

    /**
     * 验证值是否是有效邮箱
     *
     * @param $value
     *
     * @return bool
     */
    public static function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 验证值是否是数组
     *
     * @param $value
     *
     * @return bool
     */
    public static function validateArray($value)
    {
        return is_array($value);
    }

    /**
     * 验证值是否是指定值
     *
     * @param $value    需要验证的值
     * @param $assign   指定验证值的区间范围
     *
     * @return bool
     */
    public static function validateAssignValue($value, $assign)
    {
        return in_array($value, $assign);
    }

    /**
     * 验证值是否是数字 | 或者已设置 | 或者不为空
     */
    public static function validateIsNumericOrIsSetOrNoEmpty($value)
    {
       return self::validateNumeric($value) && self::validateIsSetOrNoEmpty($value);
    }

    /**
     * 组合验证值是否是指定值
     *
     * @param $data     需要验证的值  arrays
     *
     * @return bool
     */
    public static function validateGroupValue(array $data = [])
    {
        foreach ($data as $key => $vls) {
           $validate_result =  self::validateIsNumericOrIsSetOrNoEmpty($vls);

           //对验证结果进行处理
           if (!$validate_result) return false;
        }

        return true;
    }
}
