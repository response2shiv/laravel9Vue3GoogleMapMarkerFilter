<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-4">
                <ul class="list-group" style="max-height: 450px;overflow: auto;">
                    <li v-for="(person, index) in people" class="list-group-item">
                        {{ person.first_name }}
                    </li>
                </ul>
            </div>
            <div class="col-sm-8">
                <GoogleMap api-key="AIzaSyBndMU8J0y3dRDrtQsgZjq-X94WznNzmgk" style="width: 100%; height: 500px" :center="center" :zoom="15">
                    <Marker :options="{ position: center }" />
                </GoogleMap>
            </div>
        </div>
    </div>
</template>
<script>
import { defineComponent } from "vue";
import { GoogleMap, Marker } from "vue3-google-map";
export default defineComponent({
    components: { GoogleMap, Marker },
    data() {
        return {
            center: { lat: 40.689247, lng: -74.044502 },
            people: [],
            map: '',
        };
    },
    created() {
        axios.post("/api/people").then((res) => {
            if (res.data.success) {
                this.people = res.data.data;
            } else {
                console.log(res.data.message);
                alert(res.data.message);
            }
        });
        
    },
});
</script>
