# E-commerce Microservices — Orders & Payments


### Table of contents

1. [Project overview](#project-overview)
2. [Architecture](#architecture)
    - [High-level components](#high-level-components)
    - [Communication model](#communication-model)
3. [Services](#services)
4. [Event contracts](#event-contracts)
5. [Local development](#local-development)
6. [Testing](#testing)
7. [Deployment](#deployment)
8. [Observability & logging](#observability-logging)
#
### Project overview
Short description: this repository contains microservices for an e-commerce domain focused on ordering and payments. Services communicate asynchronously using **Kafka**. Most services are implemented with **Spring Boot**; some use **.NET** and **Laravel** while following **Clean Architecture** principles. The project demonstrates domain-driven design, event-driven integration, and patterns for reliability and observability.

Goals:
 * Clear service boundaries and responsibilities
 * Reproducible local development and CI/CD
 * Well-defined event contracts and backwards-compatible changes
 * Centralized logging, metrics, and tracing

### Architecture
Include a diagram (PNG/SVG) in [/docs/architecture.png]() or link to a hosted diagram. Describe at a high level:
* API Gateway (optional)
* Order service (Laravel) - aggregate root, command and query handler, publishes domain events
* Payment service (Spring Boot) - handles payment intents
* Inventory/Stock service (Spring Boot) - subscribes to order events and reserves stock
* Notification service (Spring Boot) - sends emails based on events
* Customer service (.NET) - manages users, send welcome email when register event happend
* Kafka cluster for event streaming
* Datastores: Postgres

### Services
For each service include: name, brief responsability, stack, repo path, ports, import env variables, and how to run locally.

Example:

#### order-service
* **Responsability:** accept commands to create/update orders; validate bussines rules; publish `OrderCreated`, `OrderUpdated`, `OrderCancelled` events.
* **Stack:** Laravel, PHP 8.4.6, Eloquent ORM, PostgreSQL
* **Local port:** `8050`
* **Env:** `DATABASE_URL`, `KAFKA_URL`
* **Run:** `php artisan serve` or `docker compose up orders-service`
#

### Event contracts
