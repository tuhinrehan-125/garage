export const state = () => ({
  purchaseItems: [],
  sellItems: [],
  invoiceItems: [],
  transferItems: [],
  purchase_discount: 0,
  sell_discount: 0,
  invoice_discount: 0,
  purchase_tax: 0,
  sell_tax: 0,
  invoice_tax: 0,
  shipping_cost: 0,
  sell_shipping_cost: 0,
  transfer_shipping_cost:0
});
export const getters = {
  getPurchaseItems(state) {
    return state.purchaseItems;
  },
  getSellItems(state) {
    return state.sellItems;
  },
  getInvoiceItems(state) {
    return state.invoiceItems;
  },


  getTransferItems(state) {
    return state.transferItems;
  },
  transferTotalPrice(state,getters){
    let price = getters.transferSubTotalPrice;
    let shipping_cost = state.transfer_shipping_cost;
    let totalamount = parseInt(price) + parseInt(shipping_cost);
    return totalamount;
  },
  transferSubTotalPrice(state, getters) {
    let items = getters.getTransferItems;
    let price = 0;
    if (items && items.length) {
      items.forEach(item => {
        price += Number(item.subtotal);
      });
    }
    return price;
  },
  totalPrice(state, getters) {
    let price = getters.subTotalPrice;
    let purchase_discount = state.purchase_discount;
    let purchase_tax = state.purchase_tax;
    let shipping_cost = state.shipping_cost;
    let discount_percentage = (price * purchase_tax) / 100;
    let after_tax = price + discount_percentage;
    let after_discount = after_tax - purchase_discount;
    let totalamount = parseInt(after_discount) + parseInt(shipping_cost);
    return totalamount;
  },
  subTotalPrice(state, getters) {
    let items = getters.getPurchaseItems;
    let price = 0;
    if (items && items.length) {
      items.forEach(item => {
        price += Number(item.subtotal);
      });
    }
    return price;
  },
  sellTotalPrice(state, getters) {
    let price = getters.sellSubTotalPrice;
    let sell_discount = state.sell_discount;
    let sell_tax = state.sell_tax;
    let sell_shipping_cost = state.sell_shipping_cost;
    let sell_discount_percentage = (price * sell_tax) / 100;
    let sell_after_tax = price + sell_discount_percentage;
    let sell_after_discount = sell_after_tax - sell_discount;
    let sell_total_amount =
      parseInt(sell_after_discount) + parseInt(sell_shipping_cost);
    return sell_total_amount;
  },


  invoiceTotalPrice(state, getters) {
    let price = getters.invoiceSubTotalPrice;
    // let sell_discount = state.sell_discount;
    let invoice_discount = state.invoice_discount;
    // let sell_tax = state.sell_tax;
    let invoice_tax = state.invoice_tax;
    // let sell_shipping_cost = state.sell_shipping_cost;
    // let sell_discount_percentage = (price * sell_tax) / 100;
    let invoice_discount_percentage = (price * invoice_tax) / 100;
    // let sell_after_tax = price + sell_discount_percentage;
    let invoice_after_tax = price + invoice_discount_percentage;
    // let sell_after_discount = sell_after_tax - sell_discount;
    let invoice_after_discount = invoice_after_tax - invoice_discount;
    // let sell_after_discount = sell_after_tax - invoice_discount;
    // let sell_total_amount = parseInt(sell_after_discount) + parseInt(sell_shipping_cost);
    let invoice_total_amount = parseInt(invoice_after_discount);

    // console.log(price,invoice_total_amount)
    return invoice_total_amount;
  },

  sellSubTotalPrice(state, getters) {
    let sitems = getters.getSellItems;
    let sprice = 0;
    if (sitems && sitems.length) {
      sitems.forEach(item => {
        sprice += Number(item.subtotal);
      });
    }
    return sprice;
  },

  invoiceSubTotalPrice(state, getters) {
    let sitems = getters.getInvoiceItems;
    let sprice = 0;
    if (sitems && sitems.length) {
      sitems.forEach(item => {
        sprice += Number(item.subtotal);
      });
    }
    return sprice;
  }
};

export const mutations = {
  SET_PURCHASE_PRODUCTS(state, payload) {
    state.purchaseItems = payload;
  },
  SET_TRANSFER_PRODUCTS(state, payload) {
    state.transferItems = payload;
  },
  SET_SELL_PRODUCTS(state, payload) {
    state.sellItems = payload;
  },
  SET_INVOICE_PRODUCTS(state, payload) {
    state.invoiceItems = payload;
  },


  SET_ADVANCE(state, payload) {
    state.advance = payload;
  },
  SET_ADVANCE_PAYOUT(state, payload) {
    state.advancepayout = payload;
  },
  ADD_PURCHASE_ITEMS(state, payload) {
    let items = state.purchaseItems;
    if (items) {
      items.push(payload);
    } else {
      state.purchaseItems = [payload];
    }
  },
  ADD_SELL_ITEMS(state, payload) {
    let items = state.sellItems;
    if (items) {
      items.push(payload);
    } else {
      state.sellItems = [payload];
    }
  },

  ADD_INVOICE_ITEMS(state, payload) {
    let items = state.invoiceItems;
    if (items) {
      items.push(payload);
    } else {
      state.invoiceItems = [payload];
    }
  },
  ADD_TRANSFER_ITEMS(state, payload) {
    let items = state.transferItems;
    if (items) {
      items.push(payload);
    } else {
      state.transferItems = [payload];
    }
  },
  REMOVE_PRODUCT(state, payload) {
    let items = state.purchaseItems;
    if (items) {
      let product = items.find(product => {
        return product.id == payload.id;
      });

      if (product) {
        let index = items.indexOf(product);
        items = items.splice(index, 1);
      }
    }
  },



  SET_PURCHASE_DISCOUNT(state, payload) {
    state.purchase_discount = payload;
  },
  SET_PURCHASE_TAX(state, payload) {
    state.purchase_tax = payload;
  },
  SET_SHIPPING_COST(state, payload) {
    state.shipping_cost = payload;
  },
  SET_TRASFER_SHIPPING_COST(state,payload){
    state.transfer_shipping_cost = payload;
  },
  SET_SELL_DISCOUNT(state, payload) {
    state.sell_discount = payload;
  },

  SET_INVOICE_DISCOUNT(state, payload) {
    state.invoice_discount = payload;
  },

  SET_SELL_TAX(state, payload) {
    state.sell_tax = payload;
  },

  SET_INVOICE_TAX(state, payload) {
    state.invoice_tax = payload;
  },

  SET_SELL_SHIPPING_COST(state, payload) {
    state.sell_shipping_cost = payload;
  }
};

export const actions = {
  addItemToPurchase(
    { commit },
    {
      product,
      product_id,
      variation_id,
      purchase_quantity,
      purchase_price,
      discount,
      tax
    }
  )
  {
    let item = {
      product: product,
      product_id: product_id,
      variation_id: variation_id,
      purchase_quantity: purchase_quantity,
      purchase_price: purchase_price,
      discount: discount,
      tax: tax,
      subtotal: parseInt(purchase_price) + parseInt(tax * purchase_price) / 100
    };
    commit("ADD_PURCHASE_ITEMS", item);
  },
  addItemToSell(
    { commit },
    {
      product,
      product_id,
      variation_id,
      sell_quantity,
      sell_price,
      unit,
      discount,
      tax
    }
  )

  {
    let item = {
      product: product,
      product_id: product_id,
      variation_id: variation_id,
      sell_quantity: sell_quantity,
      sell_price: sell_price,
      unit: unit,
      discount: discount,
      tax: tax,
      subtotal: parseInt(sell_price) + parseInt(tax * sell_price) / 100
    };
    commit("ADD_SELL_ITEMS", item);
  },

  // adding invoice item start from here
  addItemToInvoice(
    { commit },
    {
      name,
      id,
      invoice_quantity,
      price,
      discount,
      tax
    }
  )
  {
    let item = {
      name: name,
      id: id,
      invoice_quantity: invoice_quantity,
      price: price,
      discount: discount,
      tax: tax,

      // subtotal: parseInt(price) + parseInt(tax * price) / 100
      subtotal: parseInt(price) + parseInt(tax * price) / 100
    };
    // commit("ADD_SELL_ITEMS", item);
    commit("ADD_INVOICE_ITEMS", item);
  },
  // adding invoice item end here


  addItemToTransfer(
    { commit },
    { product, product_id,qty_available,variation_id, quantity, unit, purchase_price }
  ) {
    let item = {
      product: product,
      product_id: product_id,
      qty_available: qty_available,
      variation_id: variation_id,
      quantity: quantity,
      unit: unit,
      purchase_price: purchase_price,
      subtotal: parseInt(purchase_price) * parseInt(quantity)
    };
    commit("ADD_TRANSFER_ITEMS", item);
  },
  //sell
  updateSellItem({ commit, getters }, payload) {
    let sellItems = getters.getSellItems;
    if (payload.type == "qtychange") {
      if (sellItems) {
        let index = payload.index;
        let newtax = sellItems[index].tax * payload.sell_quantity;
        sellItems[index].sell_quantity = payload.sell_quantity;
        sellItems[index].tax = newtax;
        sellItems[index].subtotal =
          payload.sell_quantity * sellItems[index].sell_price +
          parseInt(
            newtax * payload.sell_quantity * sellItems[index].sell_price
          ) /
            100 -
          sellItems[index].discount;

        commit("SET_SELL_PRODUCTS", sellItems);
      }
    }
    if (payload.type == "discountchange") {
      if (sellItems) {
        let index = payload.index;
        sellItems[index].discount = payload.discount;
        sellItems[index].subtotal =
          sellItems[index].sell_quantity * sellItems[index].sell_price +
          parseInt(
            sellItems[index].tax *
              sellItems[index].sell_price *
              sellItems[index].sell_quantity
          ) /
            100 -
          payload.discount;

        commit("SET_SELL_PRODUCTS", sellItems);
      }
    }
    if (payload.type == "selltax") {
      if (sellItems) {
        commit("SET_SELL_TAX", payload.tax);
      }
    }
    if (payload.type == "selldiscount") {
      if (sellItems) {
        commit("SET_SELL_DISCOUNT", payload.discount);
      }
    }

    if (payload.type == "shippingcost") {
      if (sellItems) {
        commit("SET_SELL_SHIPPING_COST", payload.shipping_cost);
      }
    }
  },
  //sell ends here


  updateInvoiceItem({ commit, getters }, payload) {
    // let sellItems = getters.getSellItems;
    let invoiceItems = getters.getInvoiceItems;
    if (payload.type == "qtychange") {
      if (invoiceItems) {
        let index = payload.index;
        let newtax = sellItems[index].tax * payload.sell_quantity;
        invoiceItems[index].invoice_quantity = payload.invoice_quantity;
        // sellItems[index].tax = newtax;
        // sellItems[index].subtotal =
        //   payload.sell_quantity * sellItems[index].sell_price +
        //   parseInt(
        //     newtax * payload.sell_quantity * sellItems[index].sell_price
        //   ) /
        //   100 -
        //   sellItems[index].discount;

        invoiceItems[index].subtotal =
          payload.invoice_quantity * invoiceItems[index].price

        commit("SET_SELL_PRODUCTS", sellItems);
      }
    }
    if (payload.type == "discountchange") {
      if (sellItems) {
        let index = payload.index;
        sellItems[index].discount = payload.discount;
        sellItems[index].subtotal =
          sellItems[index].sell_quantity * sellItems[index].sell_price +
          parseInt(
            sellItems[index].tax *
            sellItems[index].sell_price *
            sellItems[index].sell_quantity
          ) /
          100 -
          payload.discount;

        commit("SET_SELL_PRODUCTS", sellItems);
      }
    }
    if (payload.type == "selltax") {
      if (sellItems) {
        commit("SET_SELL_TAX", payload.tax);
      }
    }
    if (payload.type == "selldiscount") {
      if (sellItems) {
        commit("SET_SELL_DISCOUNT", payload.discount);
      }
    }

    if (payload.type == "shippingcost") {
      if (sellItems) {
        commit("SET_SELL_SHIPPING_COST", payload.shipping_cost);
      }
    }
  },


  //purchase

  updateCartItem({ commit, getters }, payload) {
    let purchaseItems = getters.getPurchaseItems;

    if (payload.type == "pricechange") {
      if (purchaseItems) {
        let index = payload.index;
        purchaseItems[index].purchase_price = payload.purchase_price;
        purchaseItems[index].subtotal =
          payload.purchase_price * purchaseItems[index].purchase_quantity +
          parseInt(
            purchaseItems[index].tax *
              payload.purchase_price *
              purchaseItems[index].purchase_quantity
          ) /
            100 -
          purchaseItems[index].discount;
        commit("SET_PURCHASE_PRODUCTS", purchaseItems);
      }
    }
    if (payload.type == "qtychange") {
      if (purchaseItems) {
        let index = payload.index;
        let newtax = purchaseItems[index].tax * payload.purchase_quantity;
        purchaseItems[index].purchase_quantity = payload.purchase_quantity;
        purchaseItems[index].tax = newtax;
        purchaseItems[index].subtotal =
          payload.purchase_quantity * purchaseItems[index].purchase_price +
          parseInt(
            newtax *
              payload.purchase_quantity *
              purchaseItems[index].purchase_price
          ) /
            100 -
          purchaseItems[index].discount;

        commit("SET_PURCHASE_PRODUCTS", purchaseItems);
      }
    }
    if (payload.type == "discountchange") {
      if (purchaseItems) {
        let index = payload.index;
        purchaseItems[index].discount = payload.discount;
        purchaseItems[index].subtotal =
          purchaseItems[index].purchase_quantity *
            purchaseItems[index].purchase_price +
          parseInt(
            purchaseItems[index].tax *
              purchaseItems[index].purchase_price *
              purchaseItems[index].purchase_quantity
          ) /
            100 -
          payload.discount;

        commit("SET_PURCHASE_PRODUCTS", purchaseItems);
      }
    }
    if (payload.type == "taxchange") {
      if (purchaseItems) {
        let index = payload.index;
        purchaseItems[index].tax = payload.tax;
        purchaseItems[index].subtotal =
          purchaseItems[index].purchase_quantity *
            purchaseItems[index].purchase_price +
          parseInt(
            payload.tax *
              purchaseItems[index].purchase_price *
              purchaseItems[index].purchase_quantity
          ) /
            100 -
          purchaseItems[index].discount;

        commit("SET_PURCHASE_PRODUCTS", purchaseItems);
      }
    }
    if (payload.type == "purchasetax") {
      if (purchaseItems) {
        commit("SET_PURCHASE_TAX", payload.tax);
      }
    }
    if (payload.type == "purchasediscount") {
      if (purchaseItems) {
        commit("SET_PURCHASE_DISCOUNT", payload.discount);
      }
    }
    if (payload.type == "shippingcost") {
      if (purchaseItems) {
        commit("SET_SHIPPING_COST", payload.shipping_cost);
      }
    }
  },

  //transfer
  updateTransferItem({ commit, getters }, payload) {
    let transferItems = getters.getTransferItems;

    if (payload.type == "qtychange") {
      if (transferItems) {
        let index = payload.index;
        if(parseInt(payload.quantity)>parseInt(transferItems[index].qty_available))
        {
          alert('Quantity is not available')
          transferItems[index].quantity=1
          return
        }
        transferItems[index].quantity = payload.quantity;
        transferItems[index].subtotal =
          payload.quantity * transferItems[index].purchase_price;

        commit("SET_TRANSFER_PRODUCTS", transferItems);
      }
    }

    if (payload.type == "pricechange") {
      if (transferItems) {
        let index = payload.index;
        transferItems[index].purchase_price = payload.purchase_price;
        transferItems[index].subtotal =
          payload.purchase_price * transferItems[index].quantity;
        commit("SET_TRANSFER_PRODUCTS", transferItems);
      }
    }
    if(payload.type=="shippingcost"){
      if (transferItems) {
        commit("SET_TRASFER_SHIPPING_COST", payload.shipping_cost);
      }
    }
  },
  updatePrice({ commit, getters }, { product, price, index }) {
    let purchaseItems = getters.getpurchaseItems;
    if (purchaseItems) {
      purchaseItems[index].price = price / 40;
      purchaseItems[index].total = (price / 40) * purchaseItems[index].qty;
      commit("SET_PURCHASE_PRODUCTS", purchaseItems);
    }
  },
  removeCartItem({ commit, getters }, { product, index }) {
    let purchaseItems = getters.getpurchaseItems;
    if (purchaseItems) {
      purchaseItems.splice(index, 1);
      commit("REMOVE_PRODUCT", purchaseItems);
    }
  }
};
