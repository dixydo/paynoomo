### How to run

1. Clone repository and cd into the folder

```bash
git clone git@github.com:dixydo/paynoomo.git
```

2. Create a `.env` file from `.env.example` and modify it to your needs:

```bash
cp .env.example .env
```

2. Install the dependencies:

```bash
composer install
```

3. Run tests

```bash
./vendor/bin/phpunit tests
```

