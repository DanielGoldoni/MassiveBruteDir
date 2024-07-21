# MassiveBruteDir
 BruteForce de diretórios massivo.

 Realiza uma pesquisa de diretórios em sites alvos, utilizando listas externas de URLs e wordlists de diretórios para realizar verificações de forma    
 eficiente. É uma ferramenta útil para detectar a presença de arquivos ou diretórios expostos indevidamente em múltiplos sites.

# Funcionalidades 
 Carregamento de Alvos e Wordlists: Lê listas de URLs de alvos e de diretórios a partir de arquivos de texto externos (pode-se usar outras wordlsits), permitindo   
 fácil atualização.
 
 Verificação Paralela: Utiliza cURL para realizar verificações em paralelo, aumentando a eficiência da pesquisa.
 
 Relatório de Resultados: Reporta URLs de diretórios encontrados que estão presentes nos sites alvos.

# Instalação e uso
  git clone https://github.com/DanielGoldoni/MassiveBruteDir && cd MassiveBruteDir
  
  php MassiveBruteDir.php
  
