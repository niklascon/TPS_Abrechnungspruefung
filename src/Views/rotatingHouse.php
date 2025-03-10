<x3d width='400px' height='400px'>
    <scene>
        <transform DEF="rotatingHouse" rotation="0 1 0 0">
            <!-- Base -->
            <shape>
                <appearance>
                    <material diffuseColor="0.8 0.6 0.4"></material>
                </appearance>
                <box size="4 2 4"></box>
            </shape>
            <!-- Roof of the house -->
            <transform translation="0 1 0">
                <shape>
                    <appearance>
                        <material
                                diffuseColor="0.6 0 0"
                        ></material>
                    </appearance>
                    <indexedfaceset coordIndex="1 0 2 -1  2 0 3 -1  3 0 4 -1  4 0 1 -1">
                        <coordinate point="0 2 0  -2 0 -2  2 0 -2  2 0 2  -2 0 2"></coordinate>
                    </indexedfaceset>
                </shape>
            </transform>
            <!-- Door -->
            <transform translation="0 -0.2499 2.01">
                <shape>
                    <appearance>
                        <material diffuseColor="0.3 0.1 0"></material>
                    </appearance>
                    <box size="1 1.5 0.1"></box>
                </shape>
            </transform>

            <!--Group for windows-->
            <group DEF='Window'>
                <shape>
                    <appearance>
                        <material diffuseColor="0.6 0.8 1" transparency="0.2"></material>
                    </appearance>
                    <box size="0.8 0.8 0.05"></box>
                </shape>
            </group>
            <!-- Front Wall Windows -->
            <transform translation="-1.3 0.1 2.01">
                <group USE='Window'></group>
            </transform>
            <transform translation="1.3 0.1 2.01">
                <group USE='Window'></group>
            </transform>
        </transform>
        <timeSensor DEF="rotationTimer" cycleInterval="6" loop="true"></timeSensor>
        <orientationInterpolator DEF="rotInterpolator" key="0 0.5 1" keyValue="0 1 0 0 0 1 0 3.14 0 1 0 6.28"></orientationInterpolator>
        <ROUTE fromNode="rotationTimer" fromField="fraction_changed" toNode="rotInterpolator" toField="set_fraction"></ROUTE>
        <ROUTE fromNode="rotInterpolator" fromField="value_changed" toNode="rotatingHouse" toField="set_rotation"></ROUTE>

    </scene>
</x3d>
