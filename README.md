# LumiA Backend

API Laravel do LumiA: autenticação, upload privado de currículos, fila de análise e integração com a API Gemini.

## Executar com Docker

1. Copie `.env.example` para `.env` e preencha `GEMINI_API_KEY`.
2. Na pasta `LumIAback`:

```bash
docker compose up --build
```

A API fica em `http://localhost:8000`.

Usuários iniciais:

- `admin@lumia.local` / `password`
- `aluno@lumia.local` / `password`

O worker de fila sobe no serviço `queue`. Sem ele, as análises não saem de `pending`.

## Hospedagem no Cloudflare (plano gratuito)

O Cloudflare Pages **não executa PHP**. A API continua neste Docker; o Cloudflare só publica HTTPS na frente, via Tunnel.

1. Crie um túnel em [Zero Trust > Networks > Tunnels](https://one.dash.cloudflare.com/).
2. No hostname público (ex.: `api.seudominio.com`), o serviço de origem deve ser `http://nginx:80`.
3. Cole o token do túnel em `CLOUDFLARE_TUNNEL_TOKEN` no `.env`.
4. Ajuste `APP_URL` para a URL HTTPS do túnel e `FRONTEND_URL` para a URL do Pages (`https://lumia-front.pages.dev`).
5. Suba o túnel:

```bash
docker compose --profile cloudflare up --build
```

Sem domínio próprio, um Quick Tunnel também funciona:

```bash
docker run --rm --network lumiaback_lumia cloudflare/cloudflared:latest tunnel --url http://nginx:80
```
