# Tour de App 2024 - Error Makers

### Grandfinale - OpenAI API
Bylo použitý jen v /activities v search baru když uživatel něco napíše, tak to vypíše aktivity podle zadaných slov.



### Přihlašovací údaje pro učitelský účty (některý 😀)
- **login**: `vitakriz`\
   **password**: `Víťa Kříž`
- **login**: `valek`\
  **password**: `LadislavValek2002`



--- 



## Jak použít docker a spustit projekt?
1. nainstaluj WSL a [Docker Desktop](https://www.docker.com/products/docker-desktop/)
   ```ps
   $ wsl --install
   ```  
2. root složku projektu otevři v cmd a napiš tyto příkazy
   ```ps
   $ docker build . -t tda-php
   ```
   ```ps
   $ docker run -p 8080:80 tda-php
   ```
3. zobrazení všech aktivních procesů
    ```ps
    $ docker ps
    ```
4. stopnutí procesů
    ```ps
    $ docker stop <id>
    ```