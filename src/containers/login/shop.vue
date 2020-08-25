<template>
  <div class="main">
    <div class="leftNav">
      <div v-for="(item,i) in leftNav" :key="i" class="leftItem">{{item}}</div>
    </div>
    <div class="righht">
      <div class="foodList">
        <div class="foodItem" v-for="(item,i) in foodList" :key="i">
          <img :src="item.imgSrc" alt />
          <div class="txtPrice">
            <div class="txt">{{item.name}}</div>
            <div class="price">{{item.price}}.00/份</div>
          </div>
          <div class="btns">
            <div
              class="lBtn"
              @click="jian(item)"
              :class="{
            'dis':item.num==0
            }"
            >-</div>
            <div class="num">{{item.num}}</div>
            <div class="jBtn" @click="jia(item)">+</div>
          </div>
        </div>
      </div>
      <div class="foot">
        <div class="allPrice">价格： {{allPrice}}</div>

        <div class="jiesuan">去结算</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // allPrice:'0',
      leftNav: ["美食", "主食", "素菜", "荤菜", "早餐"],
      foodList: [
        {
          name: "南瓜饭",
          price: "15",
          imgSrc: require("./img/shop/msngf1.jpg"),
          num: 0,
        },
        {
          name: "锅贴",
          price: "12",
          imgSrc: require("./img/shop/msgt1.jpg"),
          num: 0,
        },
        {
          name: "炸鸡块",
          price: "14",
          imgSrc: require("./img/shop/mszhajikuai.jpg"),
          num: 0,
        },
        {
          name: "红烧肉",
          price: "13",
          imgSrc: require("./img/shop/mshongshaorou.jpg"),
          num: 0,
        },
      ],
    };
  },
  computed: {
    allPrice() {
      let allPrice = 0;
      this.foodList.map((v) => {
        console.log("v: ", v);
        allPrice += v.price * v.num;
      });
      return allPrice;
    },
  },
  methods: {
    jia(item) {
      ++item.num;
    },
    jian(item) {
      if (item.num == 0) {
        return false;
      }
      --item.num;
    },
  },
};
</script>

<style>
.main {
  display: flex;
}
.leftNav {
  display: flex;
  flex-direction: column;
  width: 150px;
  margin-right: 20px;
}
.leftItem {
  text-align: center;
  background: #f7ecec;
  border-bottom: 3px solid #e3dddd;
  box-sizing: border-box;
}
.leftItem:last-child {
  border-bottom: none;
}
.foodList {
  background: #f8f9fa;
  width: 400px;
  height: 400px;
  padding: 30px;
}
.foodItem {
  display: flex;
  border-bottom: 1px solid #e3dddd;
  box-sizing: border-box;
  padding: 20px;
  justify-content: space-around;
}
.foodItem img {
  width: 40px;
  height: 40px;
}
.btns {
  display: flex;
}
.dis {
  opacity: 0.5;
}
.lBtn,
.jBtn {
  width: 20px;
  height: 20px;
  border-radius: 20px;
  text-align: center;
  background: #f8f9fa;
}
.num {
  width: 30px;
  height: 30px;
  text-align: center;
  border: 1px solid #000;
}
.foot {
  width: 100%;
  display: flex;
  height: 50px;
  line-height: 50px;
}
.allPrice {
  width: 70%;
  background: black;
  color: #fff;
  text-align: left;
  padding-left: 30px;
  border-radius: 10opx;
}
.jiesuan {
  width: 30%;
  background: yellow;
  border-radius: 10opx;
  padding-left: 20px;
}
.righht {
  display: flex;
  flex-direction: column;
}
</style>