<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define Roles
        $roles = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'editor' => 'Editor',
            'reporter' => 'Reporter',
            'moderator' => 'Moderator',
            'ad_manager' => 'Ad Manager',
            'reader' => 'Reader',
        ];

        $roleModels = [];
        foreach ($roles as $name => $label) {
            $roleModels[$name] = Role::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        // Define Permissions
        $permissions = [
            // News
            ['name' => 'view-news', 'label' => 'View News', 'group' => 'news'],
            ['name' => 'create-news', 'label' => 'Create News', 'group' => 'news'],
            ['name' => 'edit-news', 'label' => 'Edit News', 'group' => 'news'],
            ['name' => 'publish-news', 'label' => 'Publish News', 'group' => 'news'],
            ['name' => 'delete-news', 'label' => 'Delete News', 'group' => 'news'],
            // Taxonomy
            ['name' => 'manage-categories', 'label' => 'Manage Categories', 'group' => 'taxonomy'],
            ['name' => 'manage-tags', 'label' => 'Manage Tags', 'group' => 'taxonomy'],
            ['name' => 'manage-locations', 'label' => 'Manage Locations', 'group' => 'taxonomy'],
            ['name' => 'manage-authors', 'label' => 'Manage Authors', 'group' => 'taxonomy'],
            // Media
            ['name' => 'manage-media', 'label' => 'Manage Media', 'group' => 'media'],
            // Homepage & Layout
            ['name' => 'manage-homepage', 'label' => 'Manage Homepage Sections', 'group' => 'layout'],
            ['name' => 'manage-menus', 'label' => 'Manage Menus', 'group' => 'layout'],
            // Ads
            ['name' => 'manage-ads', 'label' => 'Manage Advertisements', 'group' => 'ads'],
            // Engagement
            ['name' => 'moderate-comments', 'label' => 'Moderate Comments', 'group' => 'engagement'],
            // Settings & Users
            ['name' => 'manage-users', 'label' => 'Manage Staff Users', 'group' => 'system'],
            ['name' => 'manage-settings', 'label' => 'Manage System Settings', 'group' => 'system'],
        ];

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm['name']], $perm);
            // Assign to editor
            if (in_array($p->group, ['news', 'taxonomy', 'media', 'layout', 'engagement'])) {
                $roleModels['editor']->permissions()->syncWithoutDetaching([$p->id]);
            }
            // Assign to reporter
            if (in_array($p->name, ['create-news', 'view-news', 'manage-media'])) {
                $roleModels['reporter']->permissions()->syncWithoutDetaching([$p->id]);
            }
            // Assign to moderator
            if ($p->name === 'moderate-comments') {
                $roleModels['moderator']->permissions()->syncWithoutDetaching([$p->id]);
            }
            // Assign to ad manager
            if ($p->name === 'manage-ads') {
                $roleModels['ad_manager']->permissions()->syncWithoutDetaching([$p->id]);
            }
        }

        // Create Default Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@htechprovision.com'],
            [
                'name' => 'H Tech Provision Admin',
                'password' => Hash::make('Admin@123456'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->roles()->syncWithoutDetaching([$roleModels['super_admin']->id]);

        // Create Sample Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@htechprovision.com'],
            [
                'name' => 'প্রধান বার্তা সম্পাদক',
                'password' => Hash::make('Editor@123456'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $editor->roles()->syncWithoutDetaching([$roleModels['editor']->id]);

        // Create Sample Reporter
        $reporter = User::firstOrCreate(
            ['email' => 'reporter@htechprovision.com'],
            [
                'name' => 'নিজস্ব প্রতিবেদক',
                'password' => Hash::make('Reporter@123456'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $reporter->roles()->syncWithoutDetaching([$roleModels['reporter']->id]);
    }
}

