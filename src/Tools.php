<?php
namespace NFService\Asaas;

use CURLFile;
use Exception;

/**
 * Classe Tools
 *
 * Classe responsável pela comunicação com a API Asaas
 *
 * @category  NFService
 * @package   NFService\Asaas\Tools
 * @author    Diego Almeida <diego.feres82 at gmail dot com>
 * @copyright 2020 NFSERVICE
 * @license   https://opensource.org/licenses/MIT MIT
 */
class Tools
{
    /**
     * Variável responsável por armazenar os dados a serem utilizados para comunicação com a API
     * Dados como token, ambiente(produção ou homologação)
     *
     * @var array
     */
    private $config = [
        'access_token' => '',
        'production' => false,
        'debug' => false,
        'version' => 3,
        'upload' => false,
        'decode' => true
    ];

    /**
     * Metodo contrutor da classe
     *
     * @param string $token Token inicial da classe
     * @param boolean $isProduction Define se o ambiente é produção
     */
    public function __construct(string $token, bool $isProduction = true)
    {
        $this->setToken($token);
        $this->setProduction($isProduction);
    }

    /**
     * Define se a classe deve se comunicar com API de Produção ou com a API de Homologação
     *
     * @param bool $isProduction Boleano para definir se é produção ou não
     *
     * @access public
     * @return void
     */
    public function setProduction(bool $isProduction) :void
    {
        $this->config['production'] = $isProduction;
    }

    /**
     * Define se a classe realizará um upload
     *
     * @param bool $isUpload Boleano para definir se é upload ou não
     *
     * @access public
     * @return void
     */
    public function setUpload(bool $isUpload) :void
    {
        $this->config['upload'] = $isUpload;
    }

    /**
     * Define se a classe realizará o decode do retorno
     *
     * @param bool $decode Boleano para definir se fa decode ou não
     *
     * @access public
     * @return void
     */
    public function setDecode(bool $decode) :void
    {
        $this->config['decode'] = $decode;
    }

    /**
     * Define o token a ser utilizado nas requisições para API Asaas
     *
     * @param string $token Token a ser definido
     *
     * @access public
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->config['access_token'] = $token;
    }

    /**
     * Define a versão a ser utilizado nas requisições para API Asaas
     *
     * @param string $version Versão a ser definida
     *
     * @access public
     * @return void
     */
    public function setVersion(int $version): void
    {
        if (in_array($version, [2, 3])) {
            $this->config['version'] = $version;
        }
    }

    /**
     * Recupera o token setado na classe
     *
     * @access public
     * @return void
     */
    public function getToken(): string
    {
        return $this->config['access_token'];
    }

    /**
     * Recupera o ambiente que foi setado na classe
     *
     *
     * @access public
     * @return bool
     */
    public function getProduction() : bool
    {
        return $this->config['production'];
    }

    /**
     * Recupera se é upload ou não
     *
     *
     * @access public
     * @return bool
     */
    public function getUpload() : bool
    {
        return $this->config['upload'];
    }

    /**
     * Recupera se faz decode ou não
     *
     *
     * @access public
     * @return bool
     */
    public function getDecode() : bool
    {
        return $this->config['decode'];
    }

    /**
     * Recupera a versão que foi setado na classe
     *
     *
     * @access public
     * @return bool
     */
    public function getVersion() : int
    {
        return $this->config['version'];
    }

    /**
     * Função responsável por definir se está em modo de debug ou não a comunicação com a API
     * Utilizado para pegar informações da requisição
     *
     * @param bool $isDebug Boleano para definir se é produção ou não
     *
     * @access public
     * @return void
     */
    public function setDebug(bool $isDebug) : void
    {
        $this->config['debug'] = $isDebug;
    }

    /**
     * Retorna os cabeçalhos padrão para comunicação com a API
     *
     * @access private
     * @return array
     */
    private function getDefaultHeaders() :array
    {
        $headers = [
            'access_token: '.$this->config['access_token']
        ];

        if (!$this->config['upload']) {
            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Type: multipart/form-data';
        }

        return $headers;
    }

    /**
     * Criar Webhook
     *
     * @access public
     * @param array $params
     * @return array
     */
    public function criaWebhook(array $params) : array
    {
        try {
            return $this->post('webhooks', $params);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Listar Webhooks
     *
     * @access public
     * @return array
     */
    public function listarWebhooks() : array
    {
        try {
            return $this->get('webhooks');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Recuperar um único Webhook
     *
     * @access public
     * @param string $id
     * @return array
     */
    public function recuperaWebhook(string $id) : array
    {
        try {
            return $this->get('webhooks/'.$id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Atualizar Webhook existente
     *
     * @access public
     * @param string $id
     * @param array $params
     * @return array
     */
    public function atualizaWebhook(string $id, array $params) : array
    {
        try {
            return $this->put('webhooks/'.$id, $params);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remover Webhook
     *
     * @access public
     * @param string $id
     * @return array
     */
    public function removeWebhook(string $id) : array
    {
        try {
            return $this->delete('webhooks/'.$id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Função responsável por retornar o saldo atual
     *
     * @access public
     * @return array
     */
    public function consultaSaldo(array $params = []): array
    {
        try {
            return $this->get('finance/getCurrentBalance', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }


    /**
     * Função responsável por retornar o extrato
     *
     * @access public
     * @return array
     */
    public function consultaExtrato(array $dados, array $params = []): array
    {
        try {
            $params = array_filter($params, function ($v, $k) {
                return $k !== 'limit' && $v['value'] !== '';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $params = array_merge($params, [
                [
                    'name' => 'startDate',
                    'value' => $dados['startDate']
                ],
                [
                    'name' => 'finishDate',
                    'value' => $dados['finishDate']
                ]
            ]);

            return $this->get('financialTransactions', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cadastrar uma empresa no ASAAS
     *
     * @param array $data Dados da empresa para o cadastro
     * @param array $params Parametros adicionais para a requsição
     *
     * @access public
     * @return array
     */
    public function cadastraEmpresa(array $data, array $params = []): array
    {
        $errors = [];
        if (isset($data['name']) && empty($data['name'])) {
            $errors[] = 'O Nome da conta é obrigatório';
        }
        if (isset($data['email']) && empty($data['email'])) {
            $errors[] = 'O email da conta é obrigatório';
        }
        if (isset($data['cpfCnpj']) && empty($data['cpfCnpj'])) {
            $errors[] = 'O CPF/CNPJ do proprietário da conta é obrigatório';
        }
        if (isset($data['phone']) && empty($data['phone'])) {
            $errors[] = 'O telefone fixo do proprietário da conta é obrigatório';
        }
        if (isset($data['mobilePhone']) && empty($data['mobilePhone'])) {
            $errors[] = 'O telefone celular do proprietário da conta é obrigatório';
        }
        if (isset($data['address']) && empty($data['address'])) {
            $errors[] = 'O logradouro do endereço do proprietário da conta é obrigatório';
        }
        if (isset($data['addressNumber']) && empty($data['addressNumber'])) {
            $errors[] = 'O número do endereço do proprietário da conta é obrigatório';
        }
        if (isset($data['province']) && empty($data['province'])) {
            $errors[] = 'O bairro do endereço do proprietário da conta é obrigatório';
        }
        if (isset($data['postalCode']) && empty($data['postalCode'])) {
            $errors[] = 'O CEP do endereço do proprietário da conta é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'name', 'email', 'loginEmail', 'cpfCnpj', 'companyType', 'phone', 'mobilePhone', 'address','addressNumber', 'complement', 'province', 'postalCode' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('accounts', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por enviar os documentos de uma empresa no ASAAS
     *
     * @param array $data Dados do documento a ser enviado
     * @param array $params Parametros adicionais para a requsição
     *
     * @access public
     * @return array
     */
    public function enviaDocumento(array $data, array $params = []): array
    {
        $errors = [];
        if (isset($data['documentType']) && empty($data['documentType'])) {
            $errors[] = 'O tipo do documento é obrigatório';
        }
        if (isset($data['documentGroupType']) && empty($data['documentGroupType'])) {
            $errors[] = 'O grupo do documento é obrigatório';
        }
        if (isset($data['documentFile']) && empty($data['documentFile'])) {
            $errors[] = 'O conteúdo do documento é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'documentType', 'documentGroupType', 'documentFile' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('documents', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            if ($data['httpCode'] === 403) {
                throw new Exception('Requisição não autorizada', 1);
            }

            throw new Exception(json_encode($data), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por retornar os status da empresa
     *
     * @access public
     * @return array
     */
    public function consultaStatusEmpresa(array $params = []): array
    {
        try {
            return $this->get('myAccount/status', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }


    /**
     * Função responsável por retornar os dados comerciais
     *
     * @access public
     * @return array
     */
    public function consultaDadosComerciais(array $params = []): array
    {
        try {
            return $this->get('myAccount/commercialInfo', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por retornar o status dos documentos
     *
     * @access public
     * @return array
     */
    public function consultaStatusDocumentos(array $params = []): array
    {
        try {
            return $this->get('myAccount/status/documentation', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por retornar os documentos envados
     *
     * @access public
     * @return array
     */
    public function buscaDocumentos(array $params = []): array
    {
        try {
            return $this->get('documents', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar as empresas do ASAAS
     *
     * @access public
     * @return array
     */
    public function listaEmpresas(array $params = []):array
    {
        try {
            $params = array_filter($params, function ($v, $k) {
                return $k !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            return $this->get('accounts', $params);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cadastrar um cliente no ASAAS
     *
     * @param array $data Dados a serem enviados para a API
     * @param array $params Parametros adicionais para a requisição
     *
     * @return array
     * @access public
     */
    public function cadastraCliente(array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['name']) || empty($data['name'])) {
            $errors[] = 'O Nome do cliente é obrigatório';
        }
        if (!isset($data['cpfCnpj']) || empty($data['cpfCnpj'])) {
            $errors[] = 'O CPF/CNPJ do cliente é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'name', 'email', 'cpfCnpj', 'phone', 'mobilePhone', 'address', 'addressNumber', 'complement', 'province', 'postalCode', 'externalReference', 'notificationDisabled', 'additionalEmails', 'municipalInscription', 'stateInscription', 'observations', 'groupName' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('customers', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar um cliente específico
     *
     * @param string $id ID do cliente desejado
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaCliente(string $id, array $params = []): array
    {
        try {
            $data = $this->get("customers/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Cliente não encontrado', 1);
            }

            if ($data['httpCode'] == 401) {
                throw new Exception('Sem comunicação com o Asaas', 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar o cliente, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar os clientes de uma empresa
     *
     * @param array $params Parametros adicionais para a requisição
     * Filtros possíveis:
     *     name, email, cpfCnpj, groupName, externalReference, offset e limit
     * Passando um filtro:
     *     [
     *         [
     *             'name' => 'cpfCnpj,
     *             'value' => '12345678996'
     *         ]
     *     ]
     *
     * @access public
     * @return array
     */
    public function listaClientes(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get('customers', $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            throw new Exception('Ocorreu um erro interno ao tentar listar os clientes, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por atualizar um cliente existente
     *
     * @param string $id ID do cliente a ser atualizado
     * @param array $data Dados de atualização do cliente
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function atualizaCliente(string $id, array $data, array $params = []): array
    {
        $errors = [];
        if (isset($data['name']) && empty($data['name'])) {
            $errors[] = 'O Nome do cliente é obrigatório';
        }
        if (isset($data['cpfCnpj']) && empty($data['cpfCnpj'])) {
            $errors[] = 'O CPF/CNPJ do cliente é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'name', 'email', 'cpfCnpj', 'phone', 'mobilePhone', 'address', 'addressNumber', 'complement', 'province', 'postalCode', 'externalReference', 'notificationDisabled', 'additionalEmails', 'municipalInscription', 'stateInscription', 'observations', 'groupName' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("customers/$id", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            return $data;

            if ($data['httpCode'] == 404) {
                throw new Exception('Cliente não encontrado', 1);
            }

            if ($data['httpCode'] == 401) {
                throw new Exception('Sem comunicação com o Asaas', 1);
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por criar uma cobrança no Asaas
     *
     * @param array $data Dados da cobrança
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function geraCobranca(array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['customer']) || empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (!isset($data['billingType']) || empty($data['billingType']) || !in_array($data['billingType'], [ 'BOLETO', 'CREDIT_CARD', 'PIX', 'UNDEFINED' ])) {
            $errors[] = 'O campo billingType é obrigatório e aceita apenas os valores BOLETO, CREDIT_CARD, PIX e UNDEFINED';
        }
        if (!isset($data['value']) || empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (!isset($data['dueDate']) || empty($data['dueDate'])) {
            $errors[] = 'O campo dueDate é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'value', 'dueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('payments', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Função responsável por criar uma cobrança já paga com cartão de crédito no Asaas
     *
     * @param array $data Dados da cobrança
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function geraCobrancaCartaoCredito(array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['customer']) || empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (!isset($data['value']) || empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (!isset($data['dueDate']) || empty($data['dueDate'])) {
            $errors[] = 'O campo dueDate é obrigatório';
        }
        //Informações do cartão de crédito
        if (!isset($data['creditCard']) || empty($data['creditCard'])) {
            $errors[] = 'O campo creditCard é obrigatório';
        } else {
            //Nome impresso no cartão
            if (!isset($data['creditCard']['holderName']) || empty($data['creditCard']['holderName'])) {
                $errors[] = 'O campo holderName dentro de creditCard é obrigatório';
            }
            //Número do cartão
            if (!isset($data['creditCard']['number']) || empty($data['creditCard']['number'])) {
                $errors[] = 'O campo number dentro de creditCard é obrigatório';
            }
            //Mês de expiração do cartão
            if (!isset($data['creditCard']['expiryMonth']) || empty($data['creditCard']['expiryMonth'])) {
                $errors[] = 'O campo expiryMonth dentro de creditCard é obrigatório';
            }
            //Ano de expiração do cartão
            if (!isset($data['creditCard']['expiryYear']) || empty($data['creditCard']['expiryYear'])) {
                $errors[] = 'O campo expiryYear dentro de creditCard é obrigatório';
            }
            //Código ccv do cartão
            if (!isset($data['creditCard']['ccv']) || empty($data['creditCard']['ccv'])) {
                $errors[] = 'O campo ccv dentro de creditCard é obrigatório';
            }
        }
        //Informações do titular do cartão de crédito
        if (!isset($data['creditCardHolderInfo']) || empty($data['creditCardHolderInfo'])) {
            $errors[] = 'O campo creditCardHolderInfo é obrigatório';
        } else {
            //Nome do titular do cartão
            if (!isset($data['creditCardHolderInfo']['name']) || empty($data['creditCardHolderInfo']['name'])) {
                $errors[] = 'O campo name dentro de creditCardHolderInfo é obrigatório';
            }
            //Email do titular do cartão
            if (!isset($data['creditCardHolderInfo']['email']) || empty($data['creditCardHolderInfo']['email'])) {
                $errors[] = 'O campo email dentro de creditCardHolderInfo é obrigatório';
            }
            //CPF/CNPJ do titular do cartão
            if (!isset($data['creditCardHolderInfo']['cpfCnpj']) || empty($data['creditCardHolderInfo']['cpfCnpj'])) {
                $errors[] = 'O campo cpfCnpj dentro de creditCardHolderInfo é obrigatório';
            }
            //CEP do títular do cartão
            if (!isset($data['creditCardHolderInfo']['postalCode']) || empty($data['creditCardHolderInfo']['postalCode'])) {
                $errors[] = 'O campo postalCode dentro de creditCardHolderInfo é obrigatório';
            }
            //Número do endereço do titular do cartão
            if (!isset($data['creditCardHolderInfo']['addressNumber']) || empty($data['creditCardHolderInfo']['addressNumber'])) {
                $errors[] = 'O campo addressNumber dentro de creditCardHolderInfo é obrigatório';
            }
            //Telefone do titular do cartão
            if (!isset($data['creditCardHolderInfo']['phone']) || empty($data['creditCardHolderInfo']['phone'])) {
                $errors[] = 'O campo phone dentro de creditCardHolderInfo é obrigatório';
            }
        }
        if (!isset($data['remoteIp']) || empty($data['remoteIp'])) {
            $errors[] = 'O campo remoteIp é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data['billingType'] = 'CREDIT_CARD';
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'value', 'installmentCount', 'installmentValue', 'dueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService', 'creditCard', 'creditCardHolderInfo', 'remoteIp' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('payments', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar uma cobrança específica
     *
     * @param string $id ID da cobrança
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaCobranca(string $id, array $params = []): array
    {
        try {
            $data = $this->get("payments/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Cobrança não encontrada', 1);
            }

            if ($data['httpCode'] == 401) {
                throw new Exception('Sem comunicação com o Asaas', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);
            }

            if (!empty($errors)) {
                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar a cobrança Asaas, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar as cobranças de uma empresa
     *
     * @param array $params Parametros adicionais para a requisição
     * Filtros possíveis:
     *     customer, billingType, status, subscription, installment, externalReference, paymentDate, antecipate,
     *     paymentDate[ge], paymentDate[le], dueDate[ge], dueDate[le], offset e limit
     * Passando um filtro:
     *     [
     *         [
     *             'name' => 'customer,
     *             'value' => 'cus_000004573220'
     *         ]
     *     ]
     *
     * @access public
     * @return array
     */
    public function listaCobrancas(array $dados, array $params = []): array
    {
        try {
            $params = array_filter($params, function ($v, $k) {
                return $k !== 'limit' && $v['value'] !== '';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $params = array_merge($params, [
                [
                    'name' => 'customer',
                    'value' => $dados['customer']
                ],
                [
                    'name' => 'dueDate[ge]',
                    'value' => $dados['dueDateStart']
                ],
                [
                    'name' => 'dueDate[le]',
                    'value' => $dados['dueDateEnd']
                ],
                [
                    'name' => 'status',
                    'value' => $dados['status']
                ]
            ]);

            $data = $this->get('payments', $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            throw new Exception('Ocorreu um erro interno ao tentar listar as cobranças, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por atualizar uma cobrança existente no Asaas
     *
     * @param string $id ID da cobrança a ser atualizada
     * @param array $data Dados da cobrança
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function atualizaCobranca(string $id, array $data, array $params = []): array
    {
        $errors = [];
        if (isset($data['customer']) && empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (isset($data['billingType']) && (empty($data['billingType']) || !in_array($data['billingType'], [ 'BOLETO', 'CREDIT_CARD', 'PIX', 'UNDEFINED' ]))) {
            $errors[] = 'O campo billingType é obrigatório e aceita apenas os valores BOLETO, CREDIT_CARD, PIX e UNDEFINED';
        }
        if (isset($data['value']) && empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (isset($data['dueDate']) && empty($data['dueDate'])) {
            $errors[] = 'O campo dueDate é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'value', 'dueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("payments/$id", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 400) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                return [
                    'httpCode' => $data['httpCode'],
                    'errors' => $errors
                ];
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por simular a antecipação de uma cobrança existente no Asaas
     *
     * @param string $id ID da cobrança no Asaas
     * @return array
     */
    public function simulaAntecipacaoCobranca(string $id) :array
    {
        try {
            if (empty($id)) {
                throw new Exception("Não informado ID da cobrança a ser antecipada", 1);
            }

            $data = [
                "payment" => $id
            ];
            $data = $this->post("anticipations/simulate/", $data);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors));
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Função responsável por simular a antecipação de uma cobrança existente no Asaas
     *
     * @param array $dados Dados para realizar antecipação
     * @return array
     */
    public function antecipacaoCobranca(array $dados) :array
    {
        try {
            $errors = [];
            if (!isset($dados['solicitante']) || empty($dados['solicitante'])) {
                $errors[] = 'Não informado solicitante da antecipação';
            }
            if (!isset($dados['cobranca']) || empty($dados['cobranca'])) {
                $errors[] = 'Não informado ID da cobrança para antecipação';
            }
            if (!empty($errors)) {
                throw new Exception(implode("\r\n", $errors));
            }

            $data = [
                "agreementSignature" => $dados['solicitante'],
                "payment" => $dados['cobranca']
            ];
            if (isset($dados['documento']) && !empty($dados['documento'])) {
                $data['documents'] = new CURLFile($dados['documento']['path'], $dados['documento']['type'], $dados['documento']['name']);
            }
            $this->setUpload(true);
            $data = $this->post("anticipations/", $data);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);
            } else {
                $errors = ['Ocorreu um erro interno'];
            }

            throw new Exception(implode("\r\n", $errors));
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Função responsável por consultar a antecipação de uma cobrança existente no Asaas
     *
     * @param string $antecipacao_id ID da antecipação no asaas
     * @return array
     */
    public function buscaAntecipacaoCobranca(string $antecipacao_id)
    {
        try {
            if (empty($antecipacao_id)) {
                throw new Exception("Não informado ID da antecipação", 1);
            }
            $data = $this->get("anticipations/$antecipacao_id");

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);
            } else if ($data['httpCode'] == 404) {
                $errors = ['Antecipação não encontrada'];
            } else {
                $errors = ['Ocorreu um erro interno'];
            }

            throw new Exception(implode("\r\n", $errors));
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Função responsável por estornar uma cobrança Asaas
     *
     * @param string $id ID da cobrança a ser estornada
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function estornaCobranca(string $id, array $params = []): array
    {
        try {
            $data = $this->post("payments/$id/refund", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar estornar a cobrança, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por remover uma cobrança Asaas
     *
     * @param string $id ID da cobrança a ser removida
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function removeCobranca(string $id, array $params = []): array
    {
        try {
            $data = $this->delete("payments/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar remover a cobrança, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por confirmar o recebimento em dinheiro de uma cobrança
     *
     * @param string $id ID da cobrança a ser confirmada
     * @param array $data Dados para o recebimento
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function confirmaRecebimentoDinheiro(string $id, array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['paymentDate']) || empty($data['paymentDate'])) {
            $errors[] = 'O campo paymentDate é obrigatório';
        }
        if (!isset($data['value']) || empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (!isset($data['notifyCustomer']) || ($data['notifyCustomer'] !== false && $data['notifyCustomer'] !== true)) {
            $errors[] = 'O campo notifyCustomer é obrigatório e é do tipo booleano';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'paymentDate', 'notifyCustomer', 'value' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("payments/$id/receiveInCash", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 400) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar confirmar recebimento da cobrança, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por criar uma cobrança parcelada no Asaas
     *
     * @param array $data Dados da cobrança
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function geraCobrancaParcelada(array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['customer']) || empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (!isset($data['billingType']) || empty($data['billingType']) || !in_array($data['billingType'], [ 'BOLETO', 'CREDIT_CARD', 'PIX', 'UNDEFINED' ])) {
            $errors[] = 'O campo billingType é obrigatório e aceita apenas os valores BOLETO, CREDIT_CARD, PIX e UNDEFINED';
        }
        if (!isset($data['installmentCount']) || empty($data['installmentCount'])) {
            $errors[] = 'O campo installmentCount é obrigatório';
        }
        if ((!isset($data['installmentValue']) || empty($data['installmentValue'])) && (!isset($data['totalValue']) || empty($data['totalValue']))) {
            $errors[] = 'O campo installmentValue é obrigatório (Caso não queira informar o valor da parcela, é obrigatório informar o valor total da cobrança pelo campo totalValue)';
        }
        if (!isset($data['dueDate']) || empty($data['dueDate'])) {
            $errors[] = 'O campo dueDate é obrigatório';
        }
        if (isset($data['creditCard'])) {
            if (!isset($data['creditCard']) || empty($data['creditCard'])) {
                $errors[] = 'O campo creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (!isset($data['creditCard']['holderName']) || empty($data['creditCard']['holderName'])) {
                    $errors[] = 'O campo holderName dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['number']) || empty($data['creditCard']['number'])) {
                    $errors[] = 'O campo number dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryMonth']) || empty($data['creditCard']['expiryMonth'])) {
                    $errors[] = 'O campo expiryMonth dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryYear']) || empty($data['creditCard']['expiryYear'])) {
                    $errors[] = 'O campo expiryYear dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['ccv']) || empty($data['creditCard']['ccv'])) {
                    $errors[] = 'O campo ccv dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (!isset($data['creditCardHolderInfo']) || empty($data['creditCardHolderInfo'])) {
                $errors[] = 'O campo creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (!isset($data['creditCardHolderInfo']['name']) || empty($data['creditCardHolderInfo']['name'])) {
                    $errors[] = 'O campo name dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['email']) || empty($data['creditCardHolderInfo']['email'])) {
                    $errors[] = 'O campo email dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['cpfCnpj']) || empty($data['creditCardHolderInfo']['cpfCnpj'])) {
                    $errors[] = 'O campo cpfCnpj dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['postalCode']) || empty($data['creditCardHolderInfo']['postalCode'])) {
                    $errors[] = 'O campo postalCode dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['addressNumber']) || empty($data['creditCardHolderInfo']['addressNumber'])) {
                    $errors[] = 'O campo addressNumber dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['phone']) || empty($data['creditCardHolderInfo']['phone'])) {
                    $errors[] = 'O campo phone dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (!isset($data['remoteIp']) || empty($data['remoteIp'])) {
                $errors[] = 'O campo remoteIp é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'totalValue', 'dueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService', 'installmentCount',  'installmentValue', 'creditCard', 'creditCardHolderInfo', 'remoteIp']);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('payments', $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar um parcelamento
     *
     * @param string $id ID da cobrança
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaParcelamento(string $id, array $params = []): array
    {
        try {
            $data = $this->get("installments/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Parcelamento não encontrado', 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar o parcelamento, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar os parcelamentos do Asaas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaParcelamentos(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get('installments', $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Parcelamentos não encontrados', 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar os parcelamentos, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por estornar um parcelamento do Asaas
     *
     * @param string $id ID da cobrança
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function estornaParcelamento(string $id, array $params = []): array
    {
        try {
            $data = $this->post("installments/$id/refund", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Parcelamentos não encontrados', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar estornar o parcelamento, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por criar uma assinatura no Asaas
     *
     * @param array $data Dados da assinatura
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function geraAssinatura(array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['customer']) || empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (!isset($data['billingType']) || empty($data['billingType']) || !in_array($data['billingType'], [ 'BOLETO', 'CREDIT_CARD', 'PIX', 'UNDEFINED' ])) {
            $errors[] = 'O campo billingType é obrigatório e aceita apenas os valores BOLETO, CREDIT_CARD, PIX e UNDEFINED';
        }
        if (!isset($data['value']) || empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (!isset($data['nextDueDate']) || empty($data['nextDueDate'])) {
            $errors[] = 'O campo nextDueDate é obrigatório';
        }
        if (!isset($data['cycle']) || empty($data['cycle']) || !in_array($data['cycle'], [ 'WEEKLY', 'BIWEEKLY', 'MONTHLY', 'QUARTERLY', 'SEMIANNUALLY', 'YEARLY' ])) {
            $errors[] = 'O campo cycle é obrigatório e aceita apenas os valores WEEKLY, BIWEEKLY, MONTHLY, QUARTERLY, SEMIANNUALLY e YEARLY';
        }
        if (isset($data['creditCard'])) {
            if (!isset($data['creditCard']) || empty($data['creditCard'])) {
                $errors[] = 'O campo creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (empty($data['creditCard']['holderName'])) {
                    $errors[] = 'O campo holderName dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['number']) || empty($data['creditCard']['number'])) {
                    $errors[] = 'O campo number dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryMonth']) || empty($data['creditCard']['expiryMonth'])) {
                    $errors[] = 'O campo expiryMonth dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryYear']) || empty($data['creditCard']['expiryYear'])) {
                    $errors[] = 'O campo expiryYear dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['ccv']) || empty($data['creditCard']['ccv'])) {
                    $errors[] = 'O campo ccv dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (!isset($data['creditCardHolderInfo']) || empty($data['creditCardHolderInfo'])) {
                $errors[] = 'O campo creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (!isset($data['creditCardHolderInfo']['name']) || empty($data['creditCardHolderInfo']['name'])) {
                    $errors[] = 'O campo name dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['email']) || empty($data['creditCardHolderInfo']['email'])) {
                    $errors[] = 'O campo email dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['cpfCnpj']) || empty($data['creditCardHolderInfo']['cpfCnpj'])) {
                    $errors[] = 'O campo cpfCnpj dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['postalCode']) || empty($data['creditCardHolderInfo']['postalCode'])) {
                    $errors[] = 'O campo postalCode dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['addressNumber']) || empty($data['creditCardHolderInfo']['addressNumber'])) {
                    $errors[] = 'O campo addressNumber dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['phone']) || empty($data['creditCardHolderInfo']['phone'])) {
                    $errors[] = 'O campo phone dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (!isset($data['remoteIp']) || empty($data['remoteIp'])) {
                $errors[] = 'O campo remoteIp é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'value', 'nextDueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService', 'cycle', 'creditCard', 'creditCardHolderInfo', 'remoteIp' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post('subscriptions', $data, $params);

            if ($data['httpCode'] == 200) {
                $params = [
                    [
                        'name' => 'subscription',
                        'value' => $data['body']->id,
                    ]
                ];
                $data = $this->get('payments', $params);

                if ($data['httpCode'] == 200) {
                    return $data;
                }
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar uma assinatura no Asaas
     *
     * @param string $id ID da assinatura no asaas
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaAssinatura(string $id, array $params = []): array
    {
        try {
            $data = $this->post("subscriptions/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Assinatura não encontrada', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar a assinatura, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar as assinaturas no Asaas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaAssinaturas(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get("subscriptions", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Nenhuma assinatura encontrada', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar listar as assinaturas, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar as cobranças de uma assinatura no Asaas
     *
     * @param string $id ID da assinatura
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaCobrancasAssinatura(string $id, array $params = []): array
    {
        try {
            $data = $this->get("subscriptions/$id/payments", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Nenhuma cobrança da assinatura encontrada', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar listar as cobranças da assinatura, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por atualizar uma assinatura no Asaas
     *
     * @param string $id ID da assinatura no asaas
     * @param array $data Dados da assinatura
     * @param array $params Parametros adicionais da requisição
     *
     * @access public
     * @return array
     */
    public function atualizaAssinatura(string $id, array $data, array $params = []): array
    {
        $errors = [];
        if (!isset($data['customer']) || empty($data['customer'])) {
            $errors[] = 'O campo customer é obrigatório';
        }
        if (!isset($data['billingType']) || empty($data['billingType']) || !in_array($data['billingType'], [ 'BOLETO', 'CREDIT_CARD', 'PIX', 'UNDEFINED' ])) {
            $errors[] = 'O campo billingType é obrigatório e aceita apenas os valores BOLETO, CREDIT_CARD, PIX e UNDEFINED';
        }
        if (!isset($data['value']) || empty($data['value'])) {
            $errors[] = 'O campo value é obrigatório';
        }
        if (!isset($data['nextDueDate']) || empty($data['nextDueDate'])) {
            $errors[] = 'O campo nextDueDate é obrigatório';
        }
        if (!isset($data['cycle']) || empty($data['cycle']) || !in_array($data['cycle'], [ 'WEEKLY', 'BIWEEKLY', 'MONTHLY', 'QUARTERLY', 'SEMIANNUALLY', 'YEARLY' ])) {
            $errors[] = 'O campo cycle é obrigatório e aceita apenas os valores WEEKLY, BIWEEKLY, MONTHLY, QUARTERLY, SEMIANNUALLY e YEARLY';
        }
        if (isset($data['creditCard'])) {
            if (!isset($data['creditCard']) || empty($data['creditCard'])) {
                $errors[] = 'O campo creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (!isset($data['creditCard']['holderName']) || empty($data['creditCard']['holderName'])) {
                    $errors[] = 'O campo holderName dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['number']) || empty($data['creditCard']['number'])) {
                    $errors[] = 'O campo number dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryMonth']) || empty($data['creditCard']['expiryMonth'])) {
                    $errors[] = 'O campo expiryMonth dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['expiryYear']) || empty($data['creditCard']['expiryYear'])) {
                    $errors[] = 'O campo expiryYear dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCard']['ccv']) || empty($data['creditCard']['ccv'])) {
                    $errors[] = 'O campo ccv dentro de creditCard é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (isset($data['creditCardHolderInfo']) || empty($data['creditCardHolderInfo'])) {
                $errors[] = 'O campo creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            } else {
                if (!isset($data['creditCardHolderInfo']['name']) || empty($data['creditCardHolderInfo']['name'])) {
                    $errors[] = 'O campo name dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['email']) || empty($data['creditCardHolderInfo']['email'])) {
                    $errors[] = 'O campo email dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['cpfCnpj']) || empty($data['creditCardHolderInfo']['cpfCnpj'])) {
                    $errors[] = 'O campo cpfCnpj dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['postalCode']) || empty($data['creditCardHolderInfo']['postalCode'])) {
                    $errors[] = 'O campo postalCode dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['addressNumber']) || empty($data['creditCardHolderInfo']['addressNumber'])) {
                    $errors[] = 'O campo addressNumber dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
                if (!isset($data['creditCardHolderInfo']['phone']) || empty($data['creditCardHolderInfo']['phone'])) {
                    $errors[] = 'O campo phone dentro de creditCardHolderInfo é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
                }
            }
            if (!isset($data['remoteIp']) || empty($data['remoteIp'])) {
                $errors[] = 'O campo remoteIp é obrigatório para gera uma cobranca parcelada paga com cartão de crédito';
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'customer', 'billingType', 'value', 'nextDueDate', 'description', 'externalReference', 'discount', 'interest', 'fine', 'postalService', 'cycle', 'creditCard', 'creditCardHolderInfo', 'remoteIp', 'updatePendingPayments' ]);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("subscriptions/$id", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            $errors = array_map(function($item) {
                return $item->description;
            }, $data['body']->errors);

            throw new Exception(implode("\r\n", $errors), 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por criar um novo pagamento de conta
     *
     * @param array $data Dados do pagamento a ser gerado
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function geraPagamento(array $data, array $params = []): array
    {
        $errors = [];

        if (!isset($data['identificationField']) || empty($data['identificationField'])) {
            $errors[] = 'O campo identificationField é obrigatório';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'identificationField', 'scheduleDate', 'description', 'discount', 'dueDate', 'value']);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("bill", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception("Ocorreu um erro interno ao tentar gerar o pagamento, tente novamente mais tarde", 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por simular um pagamento de conta
     *
     * @param array $data Dados do pagamento a ser simulado
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function simulaPagamento(array $data, array $params = []): array
    {
        $errors = [];

        if ((!isset($data['identificationField']) || empty($data['identificationField'])) && (!isset($data['barCode']) || empty($data['barCode']))) {
            $errors[] = 'É necessário informar o campo identificationField ou o campo barCode para realizar a simulação';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'identificationField', 'barCado']);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("bill/simulate", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception("Ocorreu um erro interno ao tentar simular o pagamento, tente novamente mais tarde", 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar um pagamento de conta
     *
     * @param string $id ID do pagamento buscado
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaPagamento(string $id, array $params = []): array
    {
        try {
            $data = $this->get("bill/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Pagameto não encontrado', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar o pagamento, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar os pagamentos de contas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaPagamentos(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get("bill", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Nenhum pagamento encontrado', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar listar os pagamentos, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por cancelar um pagamento de conta
     *
     * @param string $id ID do pagamento cancelado
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function cancelaPagamento(string $id, array $params = []): array
    {
        try {
            $data = $this->post("bill/$id/cancel", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Pagameto não encontrado', 1);
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar cancelar o pagamento, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por realizar uma transferência entre bancos
     *
     * @param array $data Dados para a transferência
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function transfereOutroBanco(array $data, array $params = []): array
    {
        $errors = [];

        if ((!isset($data['value']) || empty($data['value']))) {
            $errors[] = 'O campo value é obrigatório';
        }
        if ((!isset($data['bankAccount']) || empty($data['bankAccount']))) {
            $errors[] = 'O campo bankAccount é obrigatório';
        } else {
            if ((!isset($data['bankAccount']['bank']['code']) || empty($data['bankAccount']['bank']['code']))) {
                $errors[] = 'O campo code dentro de bank dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['ownerName']) || empty($data['bankAccount']['ownerName']))) {
                $errors[] = 'O campo ownerName dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['cpfCnpj']) || empty($data['bankAccount']['cpfCnpj']))) {
                $errors[] = 'O campo cpfCnpj dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['agency']) || empty($data['bankAccount']['agency']))) {
                $errors[] = 'O campo agency dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['account']) || empty($data['bankAccount']['account']))) {
                $errors[] = 'O campo account dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['accountDigit']) || empty($data['bankAccount']['accountDigit']))) {
                $errors[] = 'O campo accountDigit dentro de bankAccount é obrigatório';
            }
            if ((!isset($data['bankAccount']['bankAccountType']) || empty($data['bankAccount']['bankAccountType']) || !in_array($data['bankAccount']['bankAccountType'], ['CONTA_CORRENTE', 'CONTA_POUPANCA']))) {
                $errors[] = 'O campo bankAccountType dentro de bankAccount é obrigatório e aceita apenas os valores CONTA_CORRENTE e CONTA_POUPANCA';
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\r\n", $errors), 1);
        }

        try {
            $data = array_filter($data, function ($v, $k) {
                return in_array($k, [ 'value', 'bankAccount']);
            }, ARRAY_FILTER_USE_BOTH);

            $data = $this->post("transfers", $data, $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception("Ocorreu um erro interno ao tentar realizar a transferência, tente novamente mais tarde", 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por listar as transferências entre bancos
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaTransferencias(array $params = []): array
    {
        try {
            $data = $this->get("transfers", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception("Ocorreu um erro interno ao tentar listar as transferências, tente novamente mais tarde", 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar uma transferência específica
     *
     * @param mixed $id ID da transferência no ASAAS
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function buscaTransferencia($id, array $params = []): array
    {
        try {
            $data = $this->get("transfers/$id", $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if (isset($data['body']->errors)) {
                $errors = array_map(function($item) {
                    return $item->description;
                }, $data['body']->errors);

                throw new Exception(implode("\r\n", $errors), 1);
            }

            throw new Exception("Ocorreu um erro interno ao tentar buscar a transferência, tente novamente mais tarde", 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar os bancos cadastrados no Asaas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaBancos(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get('banks', $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Bancos não encontrados', 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar os bancos, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Função responsável por buscar as contas, de uma empresa, cadastradas no Asaas
     *
     * @param array $params Parametros adicionais para a requisição
     *
     * @access public
     * @return array
     */
    public function listaContas(array $params = []): array
    {
        try {
            $params = array_filter($params, function($item) {
                return $item['name'] !== 'limit';
            }, ARRAY_FILTER_USE_BOTH);

            $params[] = [
                'name' => 'limit',
                'value' => 100
            ];

            $data = $this->get('bankAccounts', $params);

            if ($data['httpCode'] == 200) {
                return $data;
            }

            if ($data['httpCode'] == 404) {
                throw new Exception('Contas não encontradas', 1);
            }

            throw new Exception('Ocorreu um erro interno ao tentar buscar as contas, tente novamente mais tarde!', 1);
        } catch (Exception $error) {
            throw new Exception($error, 1);
        }
    }

    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     * @return array
     */
    private function get(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders()
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a POST Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     * @return array
     */
    private function post(string $path, array $body = [], array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => !$this->config['upload'] ? json_encode($body) : $body,
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders()
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     * @return array
     */
    private function put(string $path, array $body = [], array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders(),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => !$this->config['upload'] ? json_encode($body) : $body
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     * @return array
     */
    private function delete(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_HTTPHEADER => $this->getDefaultHeaders(),
            CURLOPT_CUSTOMREQUEST => "DELETE"
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = array_merge($opts[CURLOPT_HTTPHEADER], $headers);
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a OPTION Request
     *
     * @param string $path
     * @param array $params
     * @param array $headers Cabeçalhos adicionais para requisição
     * @return array
     */
    private function options(string $path, array $params = [], array $headers = []) :array
    {
        $opts = [
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        ];

        if (!empty($headers)) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Função responsável por realizar a requisição e devolver os dados
     *
     * @param string $path Rota a ser acessada
     * @param array $opts Opções do CURL
     * @param array $params Parametros query a serem passados para requisição
     *
     * @access private
     * @return array
     */
    private function execute(string $path, array $opts = [], array $params = []) :array
    {
        if (!preg_match("/^\//", $path)) {
            $path = '/' . $path;
        }

        $url = 'https://www.asaas.com/api/v'.$this->config['version'];
        if (!$this->config['production']) {
            $url = 'https://sandbox.asaas.com/api/v'.$this->config['version'];
        }
        $url .= $path;

        $curlC = curl_init();

        if (!empty($opts)) {
            curl_setopt_array($curlC, $opts);
        }

        if (!empty($params)) {
            $paramsJoined = [];

            foreach ($params as $param) {
                if (isset($param['name']) && !empty($param['name']) && isset($param['value']) && !empty($param['value'])) {
                    $paramsJoined[] = urlencode($param['name'])."=".urlencode($param['value']);
                }
            }

            if (!empty($paramsJoined)) {
                $params = '?'.implode('&', $paramsJoined);
                $url = $url.$params;
            }
        }

        curl_setopt($curlC, CURLOPT_URL, $url);
        curl_setopt($curlC, CURLOPT_RETURNTRANSFER, true);
        $retorno = curl_exec($curlC);
        $info = curl_getinfo($curlC);
        $return["body"] = ($this->config['decode'] || !$this->config['decode'] && $info['http_code'] != '200') ? json_decode($retorno) : $retorno;
        $return["httpCode"] = curl_getinfo($curlC, CURLINFO_HTTP_CODE);
        if ($this->config['debug']) {
            $return['info'] = curl_getinfo($curlC);
        }
        curl_close($curlC);

        return $return;
    }
}
