# Tour de App 2024 - Error Makers

### Screenshoty
![s1](/img/1.png)
![s2](/img/2.png)
![s3](/img/3.png)
![s4](/img/4.png)
![s5](/img/5.png)
![s6](/img/6.png)
![s7](/img/7.png)



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