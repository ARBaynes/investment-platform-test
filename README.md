# Investment Platform Test

## Endpoints

Assume all methods are `POST` unless specified otherwise

### Account
Note: only one account of each type can be created currently, due to the limitations of the data mocking.

#### create ISA
path: `/account/create-isa`

args:
* `accountHolder` - account holder name (string)

#### create JISA
path: `/account/create-jisa`

args:
* `accountHolder` - account holder name (string)
* `accountHolderBirthday` - account holder`s birthday in YYYY-mm-dd format (string)

#### balance
path: `/account/balance`

args: 
* `accountType` - either ISA or JISA (string)
* `accountHolder` - account holder name (string)

#### deposit
path: `/account/deposit`

args:
* `accountType` - either ISA or JISA (string)
* `accountHolder` - account holder name (string)
* `amount` - amount to deposit (float)

#### withdraw
path: `/account/withdraw`

args:
* `accountType` - either ISA or JISA (string)
* `accountHolder` - account holder name (string)
* `amount` - amount to withdraw (float)

#### shares
path: `/account/shares`

args:
* `accountType` - either ISA or JISA (string)
* `accountHolder` - account holder name (string)

### Shares

#### list
path: `/shares/list`

method: `GET`

#### create
path: `/shares/create`

args:
* `slug` - The **unique** identifier for this share
* `company` - The company that these shares are for
* `startingValue` - The value this block of shares begins with
* `startingPrice` - *Optional* The price this block of shares costs

(Note: I definitely should have included a shares amount)


#### buy
path: `/shares/buy/{slug}`,
args: 
* `{slug}` - path variable of the unique shares slug that you wish to purchase
* `accountType` - either ISA or JISA (string)
* `accountHolder` - account holder name (string)

## Setting up

1. Run `docker compose build --pull --no-cache` to build fresh images
2. Run `docker compose up` (the logs will be displayed in the current shell)
3. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker compose down --remove-orphans` to stop the Docker containers.
