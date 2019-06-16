<?php

namespace robertgarrigos\CodeGenerator;

use robertgarrigos\CodeGenerator\Support\Helpers;
use File;
use Illuminate\Support\ServiceProvider;

class CodeGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $dir = __DIR__ . '/../';

        // publish the config base file
        $this->publishes([
            $dir . 'config/laravel-code-generator.php' => config_path('laravel-code-generator.php'),
        ], 'config');

        // publish the default-template
        $this->publishes([
            $dir . 'templates/default' => $this->codeGeneratorBase('templates/default'),
        ], 'default-template');

        // publish the defaultcollective-template
        $this->publishes([
            $dir . 'templates/default-collective' => $this->codeGeneratorBase('templates/default-collective'),
        ], 'default-collective-template');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $commands =
            [
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateControllerCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateModelCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateLanguageCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateFormRequestCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateRoutesCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateMigrationCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateScaffoldCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateResourcesCommand',
            'robertgarrigos\CodeGenerator\Commands\Framework\CreateMappedResourcesCommand',
            'robertgarrigos\CodeGenerator\Commands\Resources\ResourceFileFromDatabaseCommand',
            'robertgarrigos\CodeGenerator\Commands\Resources\ResourceFileCreateCommand',
            'robertgarrigos\CodeGenerator\Commands\Resources\ResourceFileDeleteCommand',
            'robertgarrigos\CodeGenerator\Commands\Resources\ResourceFileAppendCommand',
            'robertgarrigos\CodeGenerator\Commands\Resources\ResourceFileReduceCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateIndexViewCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateCreateViewCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateFormViewCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateEditViewCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateShowViewCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateViewsCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateViewLayoutCommand',
            'robertgarrigos\CodeGenerator\Commands\Views\CreateLayoutCommand',
            'robertgarrigos\CodeGenerator\Commands\Api\CreateApiControllerCommand',
            'robertgarrigos\CodeGenerator\Commands\Api\CreateApiScaffoldCommand',
            'robertgarrigos\CodeGenerator\Commands\ApiDocs\CreateApiDocsControllerCommand',
            'robertgarrigos\CodeGenerator\Commands\ApiDocs\CreateApiDocsScaffoldCommand',
            'robertgarrigos\CodeGenerator\Commands\ApiDocs\CreateApiDocsViewCommand',
        ];

        if (Helpers::isNewerThanOrEqualTo()) {
            $commands = array_merge($commands,
                [
                    'robertgarrigos\CodeGenerator\Commands\Migrations\MigrateAllCommand',
                    'robertgarrigos\CodeGenerator\Commands\Migrations\RefreshAllCommand',
                    'robertgarrigos\CodeGenerator\Commands\Migrations\ResetAllCommand',
                    'robertgarrigos\CodeGenerator\Commands\Migrations\RollbackAllCommand',
                    'robertgarrigos\CodeGenerator\Commands\Migrations\StatusAllCommand',
                ]);
        }

        if (Helpers::isApiResourceSupported()) {
            $commands = array_merge($commands,
                [
                    'robertgarrigos\CodeGenerator\Commands\Api\CreateApiResourceCommand',
                ]);
        }

        $this->commands($commands);
    }

    /**
     * Create a directory if one does not already exists
     *
     * @param string $path
     *
     * @return void
     */
    protected function createDirectory($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }
    }

    /**
     * Get the laravel-code-generator base path
     *
     * @param string $path
     *
     * @return string
     */
    protected function codeGeneratorBase($path = null)
    {
        return base_path('resources/laravel-code-generator/') . $path;
    }
}
