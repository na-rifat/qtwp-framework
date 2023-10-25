<?php

namespace qtwp\lib;

defined('ABSPATH') or exit;

class User
{

    use FUNC;

    public static function get_by_phone($phone)
    {

    }

    public static function get_by_email($email)
    {
        return get_user_by('user_email', $email);
    }

    public static function get_by_username($username)
    {
        return get_user_by('user_login', $username);
    }

    public static function chnage_password($new_password)
    {
        wp_set_password($new_password, self::current_user()->id);
    }

    public static function current_id()
    {
        return get_current_user_id();
    }

    public static function signup()
    {
        // Generate a unique confirmation code
        $confirmation_code = wp_generate_password(16);

        // Create a username from the first and last name
        $username = strtolower(self::var ('name'));

        // Check if the username already exists
        if (username_exists($username)) {
            // If the username already exists, add a number to the end until it's unique
            $count = 1;
            while (username_exists($username . $count)) {
                $count++;
            }
            $username = $username . $count;
        }

        // Use the unique username to create a new user account
        $user_data = array(
            'user_login' => $username,
            'user_email' => $_POST['email'],
            'user_pass' => $_POST['password'],
            'first_name' => self::var ('name'),
        );
        $user_id = wp_insert_user($user_data);

        // Save the confirmation code as user meta data
        add_user_meta($user_id, 'confirmation_code', $confirmation_code);

        // Send an email to the user with a confirmation link
        $confirmation_link = site_url('/confirm-account?code=' . $confirmation_code);
        wp_mail($user_data['user_email'], 'Confirm Your Account', 'Please click this link to confirm your account: ' . $confirmation_link);

        // When the user clicks the confirmation link, update their status to 'active'
        if (isset($_GET['code'])) {
            $user = get_user_by('id', $user_id);
            $stored_code = get_user_meta($user_id, 'confirmation_code', true);
            if ($_GET['code'] === $stored_code) {
                $user->user_status = 1;
                wp_update_user($user);
                echo 'Your account has been confirmed!';
            } else {
                echo 'Invalid confirmation code.';
            }
        }
    }

    public function signin()
    {}

    public function signout()
    {

    }

    public static function current_user()
    {
        static $user = false;

        if (!$user) {
            $user = get_user_by('id', get_current_user_id());
        }

        return $user;
    }

    public static function update_meta($key, $val, $user = null)
    {
        return update_user_meta($user === null ? self::current_user()->id : $user, $key, $val);
    }

    public static function meta($key, $user = null)
    {
        if (!self::is_logged()) {
            return false;
        }

        return get_user_meta($user === null ? self::current_user()->id : $user, $key, true);
    }

    public static function is_logged()
    {
        return is_user_logged_in();
    }

    public static function first_name($user_id = null)
    {
        return self::meta('first_name', $user_id);
    }

    public static function set_first_name($val, $user_id = null)
    {
        return self::update_meta('first_name', $val, $user_id);
    }

    public static function last_name($user_id = null)
    {
        return self::meta('last_name', $user_id);
    }

    public static function set_last_name($val, $user_id = null)
    {
        return self::update_meta('last_name', $val, $user_id);
    }

    public static function email($user_id = null)
    {
        return self::meta('user_email', $user_id);
    }

    public static function set_email($val, $user_id = null)
    {
        return self::update_meta('user_email', $val, $user_id);
    }

    public static function phone($user_id = null)
    {
        return self::meta('user_phone', $user_id);
    }

    public static function set_phone($val, $user_id = null)
    {
        return self::update_meta('usre_phone', $val, $user_id);
    }

    public static function get_avatar_url($user = null)
    {
        return self::meta('profile_avatar_url', $user);
    }

}
