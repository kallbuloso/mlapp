<?php
/**
 * Cria arquivos compactados .zip
 *
 * @author Luiz Otávio Miranda <contato@todoespacoonline.com/w>
 * @param string $path Caminho para o arquivo zip que será criado
 * @param array $files Arquivos que serão adicionados ao zip
 * @param bool $deleleOriginal Apaga os arquivos originais quando true
 *
 * Exemplo de uso:
 *
 * $folder_name = 'pasta_com_arquivos';
 * $folder = glob($folder_name . '/*');
 * createZip( 'meu_arquivo.zip', $folder, false );
 */

 /**
  * Zip
  */
 class Zip {


   public function createZip (
   	$path = 'arquivo.zip',
   	$files = array(),
   	$deleleOriginal = false
   ) {
   	/**
   	 * Cria o arquivo .zip
   	 */
   	$zip = new ZipArchive;
   	$zip->open( $path, ZipArchive::CREATE);

   	/**
   	 * Checa se o array não está vazio e adiciona os arquivos
   	 */
   	if ( !empty( $files ) ) {
   		/**
   		 * Loop do(s) arquivo(s) enviado(s)
   		 */
   		foreach ( $files as $file ) {
   			/**
   			 * Adiciona os arquivos ao zip criado
   			 */
   			$zip->addFile( $file, basename( $file ) );

   			/**
   			 * Verifica se $deleleOriginal está setada como true,
   			 * se sim, apaga os arquivos
   			 */
   			if ( $deleleOriginal === true ) {
   				/**
   				 * Apaga o arquivo
   				 */
   				unlink( $file );

   				/**
   				 * Seta o nome do diretório
   				 */
   				$dirname = dirname( $file );
   			}
   		}

   		/**
   		 * Verifica se $deleleOriginal está setada como true,
   		 * se sim, apaga a pasta dos arquivos
   		 */
   		if ( $deleleOriginal === true && !empty( $dirname ) ) {
   			rmdir( $dirname );
   		}
   	}

   	/**
   	 * Fecha o arquivo zip
   	 */
   	$zip->close();

    return true;
   }


 }


?>
