import {
   Controller
} from '@hotwired/stimulus';

import { Modal } from 'bootstrap';

export default class extends Controller {
   static targets = ['modal'];
   static values = {
   };

   connect() {
      console.log('alert-controller connected!!');
      this.openModal();
   }

   openModal(event) {
      const modal = new Modal(this.modalTarget);
      modal.show();
  }
}