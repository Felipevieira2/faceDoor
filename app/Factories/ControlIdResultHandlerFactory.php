<?php

namespace App\Factories;




use App\Strategies\ControlID\ResultEvent\HandleEvents\DefaultHandler;
use App\Strategies\ControlID\ResultEvent\HandleEvents\DeleteUserHandler;
use App\Strategies\ControlID\ResultEvent\HandleEvents\CreateUserMoradorHandler;
use App\Strategies\ControlID\ResultEvent\HandleEvents\CreateUserVisitanteHandler;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;
use App\Strategies\ControlID\ResultEvent\HandleEvents\AddGroupUserHandler;
use App\Strategies\ControlID\ResultEvent\HandleEvents\UploadImageUserHandler;

class ControlIdResultHandlerFactory
{
    /**
     * Mapeamento de métodos para manipuladores específicos
     */
    private static array $handlerMap = [
        'create_user_morador' => CreateUserMoradorHandler::class,    
        'add_group_user' => AddGroupUserHandler::class,
        'create_user_visitante' => CreateUserVisitanteHandler::class,
        'delete_user' => DeleteUserHandler::class,
        'upload_image_user' => UploadImageUserHandler::class,
        'default' => DefaultHandler::class,
        // Adicione mais manipuladores conforme necessário
    ];
    
    /**
     * Cria uma instância do manipulador apropriado com base no método
     *
     * @param string $method Método/tipo do job
     * @return ControlIdJobsHandlerStrategyInterface
     */
    public static function create(string $method): ControlIdJobsHandlerStrategyInterface
    {
        // Convertendo para lowercase para garantir consistência
        $method = strtolower($method);
        
        // Verifica se existe um manipulador específico para este método
        if (isset(self::$handlerMap[$method])) {
            $handlerClass = self::$handlerMap[$method];
            return new $handlerClass();
        }

        // Retorna o manipulador padrão se não houver um específico
        return new DefaultHandler();
    }
} 