## BlueMedia PHP SDK

Kod zawarty w tym repozytorium umożliwia wykonanie transakcji oraz innych usług oferowanych przez Blue Media S.A.

Użycie SDK zalecane jest podczas implementacji własnych modułów płatności.

**Uwaga:** w wersji 1.0.0 możliwe jest wykonanie płatności oraz ITN z ograniczonym zestawem parametrów.

## Spis treści
- [Repozytorium](#repozytorium)
- [Wymagania](#wymagania)
- [Instalacja](#instalacja)
- [Konfiguracja klienta](#konfiguracja-klienta)
- [Transakcja poprzez przekierowanie na paywall](#transakcja-poprzez-przekierowanie-na-paywall)
- [Przedtransakcja](#przedtransakcja)
    * [Przedtransakcja, link do kontynuacji płatności](#przedtransakcja-link-do-kontynuacji-płatności)
    * [Przedtransakcja, brak kontynuacji](#przedtransakcja-brak-kontynuacji)
- [Szybki przelew](#szybki-przelew)
- [Obsługa ITN (Instant Transaction Notification)](#obsługa-itn-instant-transaction-notification)
    * [Obsługa ITN, utworzenie obiektu komunikatu](#obsługa-itn-utworzenie-obiektu-komunikatu)
- [Pobieranie listy aktualnie dostępnych regulaminów](#pobieranie-listy-aktualnie-dostępnych-regulaminów)
- [Pobieranie listy kanałów płatności](#pobieranie-listy-kanałów-płatności)

## Repozytorium
#### Odpalenie testów:
- `docker-compose up`
- `docker exec -it php_bm_sdk composer install`
- `docker exec -it php_bm_sdk ./vendor/bin/phpunit tests`

## Wymagania
- PHP w wersji 7.2 lub nowszej.
- Rozszerzenia PHP:
  - xmlwriter,
  - xmlreader,
  - iconv,
  - mbstring,
  - hash

## Instalacja

``` bash
$ composer require bluepayment-plugin/bm-sdk
```

*Jeżeli nie korzystasz z żadnego frameworka (np. Symfony) to jest bardzo prawdopodobne, że po instalacji będzie potrzebne ręczne załadowanie klas zgodnie z przykładem ze strony https://getcomposer.org/doc/01-basic-usage.md#autoloading*:

```php
require_once 'vendor/autoload.php';

// ..

$client = new BlueMedia\Client('ID SERWISU', 'KLUCZ WSPÓŁDZIELONY');
```

*Niezaładowanie klas w odpowiedni sposób zakóczy się błędem "Class not found".*

## Konfiguracja klienta

W celu utworzenia warstwy komunikacji należy utworzyć obiekt klasy `BlueMedia\Client` podając id serwisu oraz klucz współdzielony (przyznane przez BlueMedia).

```php
$client = new BlueMedia\Client('ID SERWISU', 'KLUCZ WSPÓŁDZIELONY');
```

Podczas tworzenia obiektu klienta, za argumentami danych serwisu można dodatkowo dodać użyty tryb szyfrowania oraz separator danych (w przypadku kiedy są nadane inne niż domyślne):

```php
$client = new BlueMedia\Client(
    'ID SERWISU', 
    'KLUCZ WSPÓŁDZIELONY',
    'sha256', // tryb hashowania, domyślnie sha256, można użyć stałej z BlueMedia\Common\Enum\ClientEnum
    '|' // separator danych, domyślnie |
);
```

## Transakcja poprzez przekierowanie na paywall
Najprostszym typem wykonania transakcji jest przekierowanie do serwisu BlueMedia wraz z danymi o transakcji. Obsługa płatności leży wtedy w całości po stronie serwisu BlueMedia.

Aby wykonać transakcję należy wywołać metodę `getTransactionRedirect`, poprawne wykonanie metody zwróci formularz który wykona przekierowanie do serwisu BlueMedia:

```php
$result = $client->getTransactionRedirect([
   'gatewayUrl' => 'https://pay-accept.bm.pl', // Adres bramki BlueMedia
   'transaction' => [
       'orderID' => '123', // Id transakcji, wymagany
       'amount' => '1.20', // Kwota transakcji, wymagany
       'description' => 'Transakcja 123-123', // Tytuł transakcji, opcjonalny
       'currency' => 'PLN', // Waluta transakcji, opcjonalny, domyślnie PLN
       'customerEmail' => 'test@hostname.domain' // Email klienta, opcjonalny, zalecany ze względu na automatyczne uzupełnienie pola po stronie serwisu BM
   ]
]);

echo $result->getData();
```

Po wykonaniu płatności, serwis BlueMedia wykona przekierowanie na skonfigurowany wcześniej adres powrotu płatności. Przekierowanie następuje poprzez żądanie HTTPS (GET) z trzema parametrami:
- ServiceID - Identyfikator serwisu
- OrderID - Identyfikator transakcji
- Hash - Suma kontrolna wyliczona na podstawie ServiceID i OrderID.

Wymagane jest, aby strona powrotu z płatności weryfikowała poprawność Hash, służy do tego metoda `doConfirmationCheck`. Należy przekazać do niej dane przesłane w żądaniu GET:

```php
$data = [
    'ServiceID' => '123456',
    'OrderID' => '123',
    'Hash' => 'df5f737f48bcef93361f590b460cc633b28f91710a60415527221f9cb90da52a'
];

$result = $client->doConfirmationCheck($data); // true | false
```

## Przedtransakcja
Metoda `doTransactionInit` rozszerza standardowy model rozpoczęcia transakcji o obsługę określonych potrzeb:
- zamówienia linku do płatności na podstawie przesłanych parametrów
- obciążenia Klienta (jeśli nie jest wymagana dodatkowa autoryzacja dokonana przez Klienta)
- zweryfikowania poprawności linku płatności, zanim Klient zostanie przekierowany do Systemu – wywołanie powoduje walidację parametrów i konfiguracji Systemu
- skrócenia linka płatności – zamiast kilku/kilkunastu parametrów, link zostaje skrócony do dwóch identyfikatorów
- ukrycia danych wrażliwych parametrów linku transakcji – przedtransakcja odbywa się backendowo, a link do kontynuacji transakcji nie zawiera danych wrażliwych, a jedynie identyfikatory kontynuacji
- użycia SDK w modelu pełnym (bezpiecznym)

Metoda przyjmuje parametry takie jak w przypadku transakcji z przekierowaniem na paywall, z tą różnicą że wysyłany jest inny nagłówek, dzięki czemu serwis BlueMedia obsługuje żądanie w inny sposób.
W odpowiedzi otrzymywany jest link do kontynuacji transakcji lub odpowiedź informująca o braku kontynuacji oraz statusem płatności.

#### Przedtransakcja, link do kontynuacji płatności

```php
$result = $client->doTransactionInit([
    'gatewayUrl' => 'https://pay-accept.bm.pl',
    'transaction' => [
        'orderID' => '123',
        'amount' => '1.20',
        'description' => 'Transakcja 123-123',
        'currency' => 'PLN',
        'customerEmail' => 'test@hostname.domain'
    ]
]);

$transactionContinue = $result->getData();

$transactionContinue->getRedirectUrl(); // https://pay-accept.bm.pl/payment/continue/9IA2UISN/718GTV5E
$transactionContinue->getStatus(); // PENDING
$transactionContinue->getOrderId(); // 123
$transactionContinue->toArray(); // [...]
// ...
```

#### Przedtransakcja, brak kontynuacji

```php
$result = $client->doTransactionInit([
    'gatewayUrl' => 'https://pay-accept.bm.pl',
    'transaction' => [
        'orderID' => '123',
        'amount' => '1.20',
        'description' => 'Transakcja 123-123',
        'gatewayID' => '1500',
        'currency' => 'PLN',
        'customerEmail' => 'test@hostname.domain',
        'customerIP' => '127.0.0.1',
        'title' => 'Test',
    ]
]);

$transactionInit = $result->getData();

$transactionInit->getConfirmation(); // NOTCONFIRMED
$transactionInit->getReason(); // MULTIPLY_PAID_TRANSACTION
$transactionInit->getOrderId(); // 123
$transactionInit->toArray(); // [...]
// ...
```

## Szybki przelew
Szybki Przelew to forma płatności, która wymaga od Klienta samodzielnego przepisania danych do przelewu dostarczanych przez System. Dane do przelewu można pozyskać dzięki metodzie `doTransactionBackground`.

W zależności od kanału płatności jaki zostanie wybrany w kontekście transakcji, metoda zwróci dane do przelewu lub gotowy formularz.

Przykład wywołania (dane do transakcji):

```php
$result = $client->doTransactionBackground([
    'gatewayUrl' => 'https://pay-accept.bm.pl',
    'transaction' => [
       'orderID' => '12345',
       'amount' => '5.12',
       'description' => 'Test transaction 12345',
       'gatewayID' => '21',
       'currency' => 'PLN',
       'customerEmail' => 'test@test.test',
       'customerIP' => '127.0.0.1',
       'title' => 'Test',
       'validityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour')),
       'linkValidityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour'))
    ]
]);

$transactionBackground = $result->getData();

$transactionBackground->getReceiverNRB(); // 47 1050 1764 1000 0023 2741 0516
$transactionBackground->getReceiverName(); // Blue Media
$transactionBackground->getBankHref(); // https://ssl.bsk.com.pl/bskonl/login.html
$transactionBackground->toArray(); // [...]
// ...
```

Przykład wywołania (formularz płatności):

```php
$result = $client->doTransactionBackground([
    'gatewayUrl' => 'https://pay-accept.bm.pl',
    'transaction' => [
       'orderID' => '12345',
       'amount' => '5.12',
       'description' => 'Test transaction 12345',
       'gatewayID' => '1500',
       'currency' => 'PLN',
       'customerEmail' => 'test@test.test',
       'customerIP' => '127.0.0.1',
       'title' => 'Test',
       'validityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour')),
       'linkValidityTime' => date('Y-m-d H:i:s', strtotime('now +5 hour'))
    ]
]);

$transactionBackground = $result->getData();

echo $transactionBackground; // <form action="https://pg-accept.blue.pl/gateway/test/index.jsp" name="formGoPBL" method="POST"><input type="hidden" name="transaction" value="758519"> (...)
```

## Obsługa ITN (Instant Transaction Notification)
Serwis BlueMedia po wykonaniu płatności wysyła na wcześniej skonfigurowany adres ITN komunikat o statusie płatności. Dane przesyłane są w formacie XML dodatkowo zakodowanym w base64.
SDK oferuje metodę `doItnIn` która w wyniku przekazania danych z serwisu BlueMedia zwraca gotowy obiekt `BlueMedia\Itn\ValueObject\ItnIn` pozwalający na użycie akcesorów lub konwersję do tablicy.
Dzięki temu obiektowi, programista może użyć danych potrzebnych np. do aktualizacji statusu płatności w bazie danych itp.

Po przetworzeniu komunikatu ITN należy przekazać odpowiedź. Służy do tego metoda `doItnInResponse` która przyjmuje obiekt `ItnIn` oraz argument informujący o potwierdzeniu transakcji.

Poniżej przykład zastosowania obsługi ITN:

```php
$result = $client->doItnIn($_POST['transactions']);

$itnIn = $result->getData();
$transactionConfirmed = $client->checkHash($itnIn);

// Jeżeli status płatności z ITN jest potwierdzony i hash jest poprawny - zakończ płatność w systemie
if ($itnIn->getPaymentStatus() === 'SUCCESS' && $transactionConfirmed) {
    $order = $this->orderRepository->find($itnIn->getOrderId());

    $order->setPaymentCompleted();
}

$itnResponse = $client->doItnInResponse($itnIn, $transactionConfirmed);

return new Response($itnResponse->getData()->toXml());
```

#### Obsługa ITN, utworzenie obiektu komunikatu
Podczas implementacji może okazać się że przed wykonaniem obsługi ITN zajdzie potrzeba np. konfiguracji klienta na podstawie danych dostępowych w oparciu o walutę.
W takim modelu programista może wspomóc się metodą `getItnObject`.

```php
$itn = Client::getItnObject($_POST['transactions']);

$itn->getCurrency(); // PLN
// ...
```

## Pobieranie listy aktualnie dostępnych regulaminów
Metoda `getRegulationList` umożliwia odpytanie o aktualną listę regulaminów wraz linkami do wyświetlenia w serwisie oraz akceptacji przez klienta.

```php
$result = $this->client->getRegulationList('https://pay-accept.bm.pl');

return $result->getData();
```

## Pobieranie listy kanałów płatności
Metoda `testGetPaywayList` umożliwia odpytanie o aktualną listę płatności.

```php
$result = $this->client->getPaywayList('https://pay-accept.bm.pl');

return $result->getData();
```
