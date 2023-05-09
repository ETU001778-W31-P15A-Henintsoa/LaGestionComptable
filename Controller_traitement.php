<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Mysession.php');

class Controller_traitement extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	//LOGIN
	public function index()
	{
		$this->load->view("login");
	}

	public function traitement_login()
	{
		$mail = $this->input->post('mail');
		$mdp = $this->input->post('mdp');

		$this->Henintsoa->verification_login($mail, $mdp);
	}

	//INSCRIPTION
	public function inscription()
	{
		$this->load->view('inscription');
	}

	public function selecting_devise()
	{
		$devisevaleur = $this->Henintsoa->devise_valeur();
		var_dump($devisevaleur);
	}

	public function deleting_devise()
	{
		$iddeviseequivalente = $this->input->post('iddevise');

		$this->Henintsoa->supprimer_devise($iddeviseequivalente);
	}

	public function inserting_deviseequivalence()
	{
		$nomdeviseequivalente = $this->input->post('nomdevise');

		$this->Henintsoa->ajouter_deviseequivalence($nomdeviseequivalente);

		$this->load->view('');
	} 

	//CODE JOURNAUX

	public function codejournaux()
	{
		$indice = $this->input->get("indice");

		$taille = 3; 

		$toutes = $this->Generalisation->selecting("codeJournaux");

		$requete ="limit $indice, $taille"; 
		$data['ligne'] = round(count($toutes)/$taille);
		$data['codejournaux'] =  $this->Henintsoa->affichageliste("codeJournaux", $requete);

		$this->load->view('templates/header');
		$this->load->view('codejournaux', $data);
		$this->load->view('templates/footer');
	}

	public function codejournauxerreurajout()
	{
		$indice = $this->input->get("indice");

		$taille = 3; 

		$toutes = $this->Generalisation->selecting("codeJournaux");

		$requete ="limit $indice, $taille"; 
		$data['ligne'] = count($toutes);
		$data['codejournaux'] =  $this->Henintsoa->affichageliste("codeJournaux", $requete);
		$data['erreurajout'] = "Les liens ne doivent pas etre vide.";

		$this->load->view('templates/header');
		$this->load->view('codejournaux', $data);
		$this->load->view('templates/footer');
	}

	public function ajouterCJ()
	{
		$nom = $this->input->post('nom');
		$designation = $this->input->post('designation');
		$this->Henintsoa->ajoutercodejournaux($nom, $designation);
	}

	public function modification_codejournaux()
	{
		$id = $this->input->get("id");
		$alldetailfacture = $this->Generalisation->selecting_specified("facture", "codejournal=" . $id);
		$taille = count($alldetailfacture);
		if ($taille > 0) {
			$url = site_url("controller_traitement/codejournauxerreursuppression");
			redirect($url);
		} else {
			$table = $this->Generalisation->selecting_specified("codejournaux", "idcodejournaux=" . $id);
			$amodifier['tableau'] = $table[0];
			$this->load->view('templates/header');
			$this->load->view('modification_codejournaux', $amodifier);
			$this->load->view('templates/footer');
		}
	}

	public function modification_codejournaux_erreur()
	{
		$id = $this->input->get("id");
		$table = $this->Generalisation->selecting_specified("codejournaux", "idcodejournaux=" . $id);
		$amodifier['tableau'] = $table[0];
		$amodifier['erreur'] = "Les champs ne doivent pas etre vide.";
		$this->load->view('templates/header');
		$this->load->view('modification_codejournaux', $amodifier);
		$this->load->view('templates/footer');
	}

	public function modifiercodeJournaux()
	{
		$nom = $this->input->post('nom');
		$code = $this->input->post('code');
		$id = $this->input->post('id');

		if ($nom == null || $code == null) {
			$url = site_url('controller_traitement/modification_codejournaux_erreur?id=' . $id);
			redirect($url);
		} else {
			$sql = " nom = '" . $nom . "', code='" . $code . "'";
			$this->Generalisation->updating("codejournaux", $sql, "idcodejournaux=" . $id);
			redirect(site_url("controller_traitement/codejournaux")."?indice=0");
		}
	}

	public function codejournauxerreursuppression()
	{
		$indice = $this->input->get("indice");

		$taille = 3; 
		$requete ="limit $indice, $taille"; 
		$toutes = $this->Generalisation->selecting("codeJournaux");

		$data['erreursuppression'] = "Vous ne puvez pas supprimer cette code journaux puisqu'il est liees avec une ecriture dans le journale.";
		$data['ligne'] = round(count($toutes)/$taille);
		$data['codejournaux'] =  $this->Henintsoa->affichageliste("codeJournaux", $requete);

		$this->load->view('templates/header');
		$this->load->view('codejournaux', $data);
		$this->load->view('templates/footer');


		$this->load->view('templates/header');
		$this->load->view('codejournaux', $data);
		$this->load->view('templates/footer');
	}

	public function supprimerCJ()
	{
		$id = $this->input->get('id');
		$this->Henintsoa->supprimercodejournaux($id);
	}

	//ACCEUIL PCG
	public function pcg_acceuil()
	{
		$indice = $this->input->post("indice");
		$taille = 3;
		$requete = "limit $indice $taille";

		$pcgg = $this->Generalisation->selecting("comptegenerale");
		$pcgt = $this->Generalisation->selecting("comptetiers");


		$data['lignepcgg'] =  round(count($pcgg)/$taille);
		$data['lignepcgt'] =  round(count($pcgt)/$taille);
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale", $requete);
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers", $requete);

		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	//AJOUT PCG
	public function ajout_pcg(){
		$type = $this->input->post('type');
		$sousgen = $this->input->post('sousgen');
		$numero = $this->input->post('numero');
		$nom = $this->input->post('nom');
		$operation = $this->input->post('operation');
		$this->Henintsoa->ajout_pcg_general($type, $sousgen, $numero, $nom, $operation);
	}

	public function ajout_pcg_vide()
	{
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreur'] = "Les Champs ne doivent pas etre vides.";

		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	public function ajout_pcg_libelle()
	{
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreur'] = "Le nom ne doit contemir que 35 caracteres";

		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	public function ajout_pcg_numero()
	{
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreur'] = "Le numero du Compte doit contenir 5 chiffres maximum et 2 chiffres minimum";

		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	public function ajout_pcg_existant()
	{
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreur'] = "Le Compte que vous avez insere existe deja.";

		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	//PCG GENERALE	
	public function modification_pcg_general()
	{
		$id = $this->input->get("id");
		$alldetailfacture = $this->Generalisation->selecting_specified("detailfacture", "pcGenerale=" . $id);
		if(count($alldetailfacture)>0){
			$url = site_url('controller_traitement/pcg_general_errorappartenance?id=' . $id);
			redirect($url);
		}else{
		$pcgg = $this->Generalisation->selecting_specified("comptegenerale", "idcomptegen=" . $id);
		$data['pcgg'] = $pcgg[0];
		$this->load->view('templates/header');
		$this->load->view("modification_pcg_general", $data);
		$this->load->view('templates/footer');
		}
	}

	public function pcg_general_errorvide()
	{
		$id = $this->input->get("id");
		$pcgg = $this->Generalisation->selecting_specified("comptegenerale", "idcomptegen=" . $id);
		
		$data['pcgg'] = $pcgg[0];
		$data['erreurpcgvide'] = "Les champs ne doivent pas etre vides";
		
		$this->load->view('templates/header');
		$this->load->view("modification_pcg_general", $data);
		$this->load->view('templates/footer');
	}
	
	public function pcg_general_errorlibelle()
	{
		$id = $this->input->get("id");
		$pcgg = $this->Generalisation->selecting_specified("comptegenerale", "idcomptegen=" . $id);
		
		$data['pcgg'] = $pcgg[0];
		$data['erreurpcgvide'] = "Le nom ne doit contenir que 35 caracteres";
		
		$this->load->view('templates/header');
		$this->load->view("modification_pcg_general", $data);
		$this->load->view('templates/footer');
	}

	public function pcg_general_errorappartenance()
	{
		$id = $this->input->get("id");
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreurappartenancepcgg'] ="Vous ne pouvez effectuer cette operation car ce pcg appartient deja a un ecriture.";
		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}

	public function modifier_pcg_generale()
	{
		$id = $this->input->post('id');
		$nom = $this->input->post('nom');
		$numero = $this->input->post('numero');
		$operation = $this->input->post('operation');

		$this->Henintsoa->modification_pcg_generale($id, $numero, $nom, $operation);
	}

	public function supprimer_pcg_general()
	{
		$id = $this->input->get("id");
		$this->Henintsoa->supprimer_pcg_generale($id);		
	}

	//PCG TIERS	
	public function modification_pcg_tier()
	{
		$id = $this->input->get("id");
		$alldetailfacture = $this->Generalisation->selecting_specified("detailfacture", "pcTiers=" . $id);
		if(count($alldetailfacture)>0){
			$url = site_url('controller_traitement/pcg_tier_errorappartenance?id=' . $id);
			redirect($url);
		}else{
			$pcgt = $this->Generalisation->selecting_specified("compteTiers", "idcompteTiers=" . $id);
			$data['pcgt'] = $pcgt[0];
			$this->load->view('templates/header');
			$this->load->view("modification_pcg_tier", $data);
			$this->load->view('templates/footer');
		}
	}
	
	public function pcg_tier_errorvide()
	{
		$id = $this->input->get("id");
		$pcgg = $this->Generalisation->selecting_specified("comptetiers", "idcompteTiers=" . $id);
		
		$data['pcgt'] = $pcgg[0];
		$data['erreurpcgvide'] = "Les champs ne doivent pas etre vides";
		
		$this->load->view('templates/header');
		$this->load->view("modification_pcg_tier", $data);
		$this->load->view('templates/footer');
	}
		
	public function pcg_tier_errorlibelle()
	{
		$id = $this->input->get("id");
		$pcgg = $this->Generalisation->selecting_specified("comptetiers", "idcompteTiers=" . $id);
		
		$data['pcgt'] = $pcgg[0];
		$data['erreurpcgvide'] = "Le nom ne doit contenir que 35 caracteres";
		
		$this->load->view('templates/header');
		$this->load->view("modification_pcg_tier", $data);
		$this->load->view('templates/footer');
	}
	
	public function pcg_tier_errorappartenance()
	{
		$id = $this->input->get("id");
		$data['pcgg'] = $this->Generalisation->selecting("comptegenerale");
		$data['pcgt'] = $this->Generalisation->selecting("comptetiers");
		$data['erreurappartenancepcgt'] ="Vous ne pouvez effectuer cette operation car ce pcg appartient deja a un ecriture.";
		$this->load->view('templates/header');
		$this->load->view("pcg_acceuil", $data);
		$this->load->view('templates/footer');
	}
	
	public function modifier_pcg_tier()
	{
		$id = $this->input->post('id');
		$nom = $this->input->post('nom');
		$numero = $this->input->post('numero');
		$operation = $this->input->post('operation');

		$this->Henintsoa->modification_pcg_tier($id, $numero, $nom, $operation);
	}
	
	public function supprimer_pcg_tier()
	{
		$id = $this->input->get("id");
		$this->Henintsoa->supprimer_pcg_tier($id);		
	}

	//GRAND LIVRE
	public function grand_livre(){
		$data['data'] = $this->Henintsoa->tousdonneePcgg();
		$data['datapcgt'] = $this->Henintsoa->tousdonneePcgt();
		
		$this->load->view('templates/header');
		$this->load->view("grand_livre", $data);
		$this->load->view('templates/footer');
	}

	

	//DECONNEXION
	public function deconnexion()
	{
		session_start();
		session_destroy();
		$this->load->view('login');
	}

	//ANALYTIQUE
	public function analytique(){
		//inputs
		$incorporelle = $this->input->post("incorporelle");
		$corporelle = $this->input->post("corporelle");
		$libelle = $this->input->post("libelle");
		$prixtotale = $this->input->post("prixprixtotale");
		$fixe = $this->input->post("fixe");
		$variable = $this->input->post("variable");
		
		//avoir corporel et incorporel
		$lecorporelle = $this->Generalisation->selecting("categorie", "nomcategorie='corporel'");
		$lincorporel = $this->Generalisation->selecting("categorie", "nomcategorie='incorporel'");

		if($corporelle==true && $incorporelle==false){
			//Selection des produits
			$mesproduits = $this->Generalisation->selecting("produit");
			for ($i=0; $i < count($mesproduits) ; $i++) { 
				//insertion de charge (idcategorie, idproduit, fixe, variable, libelle, totale)
				$values= "(%s, %s, %s, %s, '%s', %s)";
				$values = sprintf($values, $lecorporelle[0]["nomcategorie"],  $mesproduits[$i]["idproduit"], ($fixe*$prixtotale)/100, ($variable*$prixtotale)/100, $libelle, $prixtotale);
				$this->Generalisation->inserting("charge", $values);

				//avoir le dernier charge (ilay vao napidiritsika)
				$lescharges = $this->Generalisation->selecting("charge order by idcharge");
				$derniercharge = $lescharges[count($lescharges)-1];
				
				//insertion centre Fixe du libelle
				$lefixe = $derniercharge->fixe;
				$mescentres = $this->Generalisation->selecting("centre");
				for($j=0; $j < count($mescentres) ; $j++) { 
					$pourcentage = $this->input->post($mesproduits[$i]["idproduit"]."fixe".$mescentres[$j]["idcentre"]);
					$sql = "(%s, %s, %s)";
					$sql = sprintf($sql, $derniercharge["idcharge"], ($lefixe*$pourcentage)/100,$mescentres[$j]["idcentre"] );
					$this->Generalisation->inserting("chargecentre", $sql);
				}
				
				//insertion centre Variable du libelle
				$levariable = $derniercharge->variable;
				$mescentres = $this->Generalisation->selecting("centre");
				for($j=0; $j < count($mescentres) ; $j++) { 
					$pourcentage = $this->input->post($mesproduits[$i]["idproduit"]."variable".$mescentres[$j]["idcentre"]);
					$sql = "(%s, %s, %s)";
					$sql = sprintf($sql, $derniercharge["idcharge"], ($levariable*$pourcentage)/100,$mescentres[$j]["idcentre"]);
					$this->Generalisation->inserting("chargecentre", $sql);
				}
			}
		}else if($corporelle==false && $incorporelle==true){
			//insertion de charge  (idcategorie, idproduit, fixe, variable, libelle, totale)
			$values= "(%s, null, null, null, '%s', %s)";
			$values = sprintf($values, $lincorporel[0]["idcategorie"], $libelle, $prixtotale);
			$this->Generalisation->inserting("chargecentre", $values);			
		}
	}

	//Affichage COUT
	public function seuilrentabilite(){
		$this->load->view('templates/header');
		$this->load->view('seuilrentabilite');
		$this->load->view('templates/footer');
	}

	public function coutderevient(){
		$this->load->view('templates/header');
		$this->load->view('coutderevient');
		$this->load->view('templates/footer');
	}
}
