# Desafío de Programación - Herramienta WC de Unix

### [Herramienta WC](https://codingchallenges.fyi/challenges/challenge-wc)

**Desafío**: Construye tu Propia Herramienta wc (conteo de palabras de Unix)

- **Lenguaje**: PHP 8.5
- **Fecha de Completado**: 20 de Diciembre de 2025
- **Descripción**:
  > Reproduce el comando Unix `wc` para contar el número de líneas,
  > palabras, bytes y caracteres en un archivo o flujo de texto.

Escribí dos versiones de este desafío:
- **Versión POO (Programación Orientada a Objetos)**
- **Versión Programática**

**Uso**:
Hay 2 formas de usar este desafío:
- usando el intérprete PHP local
- usando docker-compose:
  > docker-compose up -d
  > docker exec -it ccwc bash para ingresar al contenedor
  > cd src para entrar al directorio del desafío

**Características**:
- ✅ Contar líneas (`-l`, `--lines`)
- ✅ Contar palabras (`-w`, `--words`)
- ✅ Contar bytes (`-c`, `--bytes`)
- ✅ Contar caracteres (`-m`, `--chars`)
- ✅ Soporte para múltiples archivos
- ✅ Lectura desde STDIN
- ✅ Modo por defecto (líneas, palabras, bytes)

**Uso de la versión POO (punto de entrada ccwc)**:
```bash
# Contar líneas, palabras y bytes (por defecto)
./ccwc archivo.txt

# Contar solo líneas
./ccwc -l archivo.txt

# Contar palabras y caracteres
./ccwc -w -m archivo.txt

# Leer desde STDIN
cat archivo.txt | ./ccwc -l

# Múltiples archivos
./ccwc -l archivo1.txt archivo2.txt archivo3.txt
```

**Uso de la versión programática (punto de entrada php ccwc.php)**:
```bash
# Contar líneas, palabras y bytes (por defecto)
php ccwc.php archivo.txt

# Contar solo líneas
php ccwc.php -l archivo.txt

# Contar palabras y caracteres
php ccwc.php -w -m archivo.txt

# Leer desde STDIN
cat archivo.txt | php ccwc.php -l
```