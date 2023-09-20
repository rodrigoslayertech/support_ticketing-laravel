<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show ($filename)
    {
        // Obtenha o caminho do arquivo armazenado no sistema de arquivos
        $filePath = storage_path('app/files/' . $filename);

        // Verifique se o arquivo existe
        if (file_exists($filePath) === false) {
            abort(404);
        }

        // Retorne o arquivo para o usuário
        return response()->file($filePath);
    }
}
